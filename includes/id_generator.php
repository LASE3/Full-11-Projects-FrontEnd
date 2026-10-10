<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR - Canonical ID Generator with Idempotent Self-Healing Counters
 * Location: includes/id_generator.php
 *
 * Uses `id_counters` table with SELECT ... FOR UPDATE inside a transaction.
 * Self-heals: next_val = GREATEST(next_val, MAX(existing numeric suffix) + 1)
 */

if (!function_exists('vp_next_id')) {
    /**
     * Allocate the next sequential ID for a given domain entity.
     *
     * @param PDO $pdo Active PDO connection
     * @param string $counter Counter name in `id_counters` (e.g. 'invoices', 'tickets', 'orders')
     * @param string $prefix Prefix string (e.g. 'INV-2026-', 'CUS-', 'POL-2026-')
     * @param int $pad Number of digits to pad (e.g. 3 for '011', 4 for '1011', 0 for raw)
     * @return string
     */
    function vp_next_id(PDO $pdo, string $counter, string $prefix = '', int $pad = 0): string
    {
        $inTx = $pdo->inTransaction();
        if (!$inTx) {
            $pdo->beginTransaction();
        }

        try {
            // 1. Ensure counter row exists
            $ins = $pdo->prepare("INSERT INTO id_counters (`name`, `next_val`) VALUES (:name, 1) ON DUPLICATE KEY UPDATE `name` = `name`");
            $ins->execute([':name' => $counter]);

            // 2. Lock counter row with FOR UPDATE
            $stmt = $pdo->prepare("SELECT next_val FROM id_counters WHERE `name` = :name FOR UPDATE");
            $stmt->execute([':name' => $counter]);
            $currentVal = (int)($stmt->fetchColumn() ?: 1);

            // 3. Self-heal against actual table max
            $tableMap = [
                'invoices'   => ['table' => 'invoices',   'col' => 'inv_id',   'like' => 'INV-%'],
                'tickets'    => ['table' => 'tickets',    'col' => 'tkt_id',   'like' => 'TKT-%'],
                'documents'  => ['table' => 'documents',  'col' => 'doc_id',   'like' => 'DOC-%'],
                'orders'     => ['table' => 'orders',     'col' => 'order_id', 'is_int' => true],
                'projects'   => ['table' => 'projects',   'col' => 'prj_id',   'like' => 'PRJ-%'],
                'leads'      => ['table' => 'leads',      'col' => 'lead_id',  'is_int' => true],
                'customers'  => ['table' => 'customers',  'col' => 'cus_id',   'like' => 'CUS-%'],
                'employees'  => ['table' => 'employees',  'col' => 'emp_id',   'like' => 'EMP-%'],
                'products'   => ['table' => 'products',   'col' => 'prod_id',  'like' => 'PROD-%'],
                'risks'      => ['table' => 'board_risk_register', 'col' => 'risk_id', 'is_int' => true],
                'policies'   => ['table' => 'security_policies',   'col' => 'doc_id',  'like' => 'POL-%'],
                'compliance_controls' => ['table' => 'compliance_controls', 'col' => 'control_code', 'like' => 'CTRL-GOV-%'],
                'ops_tasks'  => ['table' => 'ops_tasks',  'col' => 'task_id',  'is_int' => true],
                'service_requests' => ['table' => 'customer_service_requests', 'col' => 'request_id', 'like' => 'SRV-%'],
            ];

            $maxExisting = 0;
            if (isset($tableMap[$counter])) {
                $info = $tableMap[$counter];
                $tbl = $info['table'];
                $col = $info['col'];

                if (!empty($info['is_int'])) {
                    $maxQuery = "SELECT MAX(`{$col}`) FROM `{$tbl}`";
                } elseif (!empty($info['like'])) {
                    $likePattern = $pdo->quote($info['like']);
                    $maxQuery = "SELECT MAX(CAST(SUBSTRING_INDEX(`{$col}`, '-', -1) AS UNSIGNED)) FROM `{$tbl}` WHERE `{$col}` LIKE {$likePattern}";
                } else {
                    $maxQuery = "SELECT MAX(CAST(SUBSTRING_INDEX(`{$col}`, '-', -1) AS UNSIGNED)) FROM `{$tbl}`";
                }

                try {
                    $maxExisting = (int)$pdo->query($maxQuery)->fetchColumn();
                } catch (Throwable) {
                    $maxExisting = 0;
                }
            }

            // Next value is GREATEST(next_val, MAX(existing numeric suffix) + 1)
            $allocatedVal = max($currentVal, $maxExisting + 1);

            // Update counter for subsequent caller
            $upd = $pdo->prepare("UPDATE id_counters SET next_val = :next WHERE `name` = :name");
            $upd->execute([
                ':next' => $allocatedVal + 1,
                ':name' => $counter
            ]);

            if (!$inTx) {
                $pdo->commit();
            }

            if ($pad > 0) {
                return $prefix . str_pad((string)$allocatedVal, $pad, '0', STR_PAD_LEFT);
            }
            return $prefix . (string)$allocatedVal;
        } catch (Throwable $e) {
            if (!$inTx && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}

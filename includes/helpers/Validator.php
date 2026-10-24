<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Input Validation & Sanitization Helper
 * Location: api/helpers/Validator.php
 *
 * Centralizes input validation/sanitization for the CRM, Customer
 * Portal, Employee Intranet, and Online Shop B2B endpoints so every
 * endpoint checks incoming input the same way instead of ad-hoc
 * `trim($data['x'] ?? '')` calls scattered per file.
 */
final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /**
     * Safely decode a JSON request body into an associative array.
     * Never throws; returns [] for empty/invalid bodies.
     *
     * @return array<string, mixed>
     */
    public static function jsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param array<string, mixed> $input
     */
    public function requiredString(array $input, string $key, int $maxLength = 255): string
    {
        $value = isset($input[$key]) ? trim((string)$input[$key]) : '';
        if ($value === '') {
            $this->errors[$key] = ucfirst(str_replace('_', ' ', $key)) . ' is required.';
            return '';
        }
        if (mb_strlen($value) > $maxLength) {
            $this->errors[$key] = ucfirst(str_replace('_', ' ', $key)) . " must not exceed {$maxLength} characters.";
            return mb_substr($value, 0, $maxLength);
        }
        return $value;
    }

    /**
     * @param array<string, mixed> $input
     */
    public function optionalString(array $input, string $key, ?string $default = null, int $maxLength = 255): ?string
    {
        if (!isset($input[$key]) || trim((string)$input[$key]) === '') {
            return $default;
        }
        $value = trim((string)$input[$key]);
        if (mb_strlen($value) > $maxLength) {
            $this->errors[$key] = ucfirst(str_replace('_', ' ', $key)) . " must not exceed {$maxLength} characters.";
            return mb_substr($value, 0, $maxLength);
        }
        return $value;
    }

    /**
     * @param array<string, mixed> $input
     */
    public function requiredInt(array $input, string $key, ?int $min = null, ?int $max = null): int
    {
        if (!isset($input[$key]) || !is_numeric($input[$key])) {
            $this->errors[$key] = ucfirst(str_replace('_', ' ', $key)) . ' must be a valid integer.';
            return 0;
        }
        $value = (int)$input[$key];
        if ($min !== null && $value < $min) {
            $this->errors[$key] = ucfirst(str_replace('_', ' ', $key)) . " must be at least {$min}.";
        }
        if ($max !== null && $value > $max) {
            $this->errors[$key] = ucfirst(str_replace('_', ' ', $key)) . " must not exceed {$max}.";
        }
        return $value;
    }

    /**
     * @param array<string, mixed> $input
     */
    public function optionalFloat(array $input, string $key, float $default = 0.0): float
    {
        if (!isset($input[$key]) || !is_numeric($input[$key])) {
            return $default;
        }
        return (float)$input[$key];
    }

    /**
     * @param array<string, mixed> $input
     */
    public function requiredEmail(array $input, string $key = 'email'): string
    {
        $value = isset($input[$key]) ? trim((string)$input[$key]) : '';
        if ($value === '' || filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[$key] = 'A valid email address is required.';
            return '';
        }
        return $value;
    }

    /**
     * Value must be one of a fixed set of allowed strings (e.g. a status enum).
     *
     * @param array<string, mixed> $input
     * @param string[] $allowed
     */
    public function requiredEnum(array $input, string $key, array $allowed): string
    {
        $value = isset($input[$key]) ? trim((string)$input[$key]) : '';
        if (!in_array($value, $allowed, true)) {
            $this->errors[$key] = ucfirst(str_replace('_', ' ', $key)) . ' must be one of: ' . implode(', ', $allowed) . '.';
        }
        return $value;
    }

    /**
     * @param array<string, mixed> $input
     * @param string[] $allowed
     */
    public function optionalEnum(array $input, string $key, array $allowed, string $default): string
    {
        $value = isset($input[$key]) ? trim((string)$input[$key]) : '';
        if ($value === '') {
            return $default;
        }
        if (!in_array($value, $allowed, true)) {
            $this->errors[$key] = ucfirst(str_replace('_', ' ', $key)) . ' must be one of: ' . implode(', ', $allowed) . '.';
        }
        return $value;
    }

    /**
     * Strict YYYY-MM-DD date validation.
     *
     * @param array<string, mixed> $input
     */
    public function requiredDate(array $input, string $key): string
    {
        $value = isset($input[$key]) ? trim((string)$input[$key]) : '';
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        if (!$date || $date->format('Y-m-d') !== $value) {
            $this->errors[$key] = ucfirst(str_replace('_', ' ', $key)) . ' must be a valid date (YYYY-MM-DD).';
            return '';
        }
        return $value;
    }

    /**
     * A safe identifier value (e.g. cus_id, prod_id): letters, digits, dash, underscore only.
     * Rejecting anything outside this charset is defense-in-depth alongside
     * prepared statements, and prevents obviously malformed IDs from
     * reaching the database layer at all.
     *
     * @param array<string, mixed> $input
     */
    public function requiredIdentifier(array $input, string $key, int $maxLength = 64): string
    {
        $value = isset($input[$key]) ? trim((string)$input[$key]) : '';
        if ($value === '' || !preg_match('/^[A-Za-z0-9_\-]{1,' . $maxLength . '}$/', $value)) {
            $this->errors[$key] = ucfirst(str_replace('_', ' ', $key)) . ' must be a valid identifier.';
            return '';
        }
        return $value;
    }

    /**
     * @param array<string, mixed> $input
     */
    public function optionalIdentifier(array $input, string $key, ?string $default = null, int $maxLength = 64): ?string
    {
        $value = isset($input[$key]) ? trim((string)$input[$key]) : '';
        if ($value === '') {
            return $default;
        }
        if (!preg_match('/^[A-Za-z0-9_\-]{1,' . $maxLength . '}$/', $value)) {
            $this->errors[$key] = ucfirst(str_replace('_', ' ', $key)) . ' must be a valid identifier.';
            return $default;
        }
        return $value;
    }

    public const MAX_LENGTHS = [
        'emp_id'           => 10,
        'cus_id'           => 10,
        'reviewer_emp_id'  => 10,
        'manager_emp_id'   => 10,
        'actor_emp_id'     => 10,
        'sales_emp_id'     => 10,
        'assigned_emp_id'  => 10,
        'owner_emp_id'     => 10,
        'target_entity_id' => 20,
        'doc_id'           => 20,
        'prod_id'          => 20,
        'order_id'         => 20,
        'inv_id'           => 20,
        'asset_id'         => 20,
        'ticket_id'        => 20,
        'quote_id'         => 20,
        'opp_id'           => 20,
        'contract_id'      => 20,
        'prj_id'           => 20,
        'dept_code'        => 10,
        'department_code'  => 10,
        'system_id'        => 10,
        'role_code'        => 20,
    ];

    /**
     * Validates input values against maximum length limits.
     * Returns an associative array of errors [field => message].
     *
     * @param array<string, mixed> $inputs
     * @param array<string, int>   $customLimits
     * @return array<string, string>
     */
    public static function validateLengths(array $inputs, array $customLimits = []): array
    {
        $limits = array_merge(self::MAX_LENGTHS, $customLimits);
        $errors = [];

        foreach ($inputs as $key => $val) {
            if ($val === null || is_array($val)) {
                continue;
            }
            $strVal = (string)$val;
            $len = mb_strlen($strVal);

            // Check known specific fields
            if (isset($limits[$key]) && $len > $limits[$key]) {
                $errors[$key] = "Field '{$key}' exceeds maximum length of {$limits[$key]} characters (received {$len}).";
                continue;
            }

            // General rule for id/code parameters: max 64 characters
            if ((str_ends_with($key, '_id') || str_ends_with($key, '_code')) && $len > 64) {
                $errors[$key] = "Identifier '{$key}' exceeds maximum length of 64 characters (received {$len}).";
            }
        }

        return $errors;
    }

    /**
     * Validates input lengths and immediately responds with 422 JSON if any field is invalid.
     *
     * @param array<string, mixed> $inputs
     * @param array<string, int>   $customLimits
     */
    public static function validateLengthsOrExit(array $inputs, array $customLimits = []): void
    {
        $errors = self::validateLengths($inputs, $customLimits);
        if (!empty($errors)) {
            $firstField = array_key_first($errors);
            self::respond422((string)$firstField, $errors[$firstField], $errors);
        }
    }

    /**
     * Terminate request with 422 Unprocessable Entity JSON.
     *
     * @param string                $field
     * @param string                $message
     * @param array<string, string> $errors
     */
    public static function respond422(string $field, string $message, array $errors = []): void
    {
        http_response_code(422);
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        if (empty($errors)) {
            $errors = [$field => $message];
        }
        echo json_encode([
            'success'     => false,
            'status_code' => 422,
            'error'       => 'Unprocessable Entity',
            'field'       => $field,
            'message'     => $message,
            'errors'      => $errors
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }
}

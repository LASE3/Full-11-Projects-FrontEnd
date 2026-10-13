<?php

/**
 * VOSTOKPRIBOR Standardized API Response Helper
 * Location: api/helpers/Response.php
 */
class Response
{
    /**
     * Send standard JSON response
     * @param bool $success
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @param array $meta
     */
    public static function send($success, $data = null, $message = '', $statusCode = 200, $meta = [])
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');

        $lang = $_GET['lang'] ?? $_COOKIE['vp_lang'] ?? 'en';
        $lang = strtolower(substr($lang, 0, 2)) === 'ar' ? 'ar' : 'en';

        $payload = [
            'success'     => (bool)$success,
            'status_code' => $statusCode,
            'message'     => $message,
            'data'        => $data,
            'rows'        => $meta['rows'] ?? (is_array($data) ? $data : []),
            'total'       => isset($meta['total']) ? (int)$meta['total'] : (is_array($data) ? count($data) : 0),
            'page'        => isset($meta['page']) ? (int)$meta['page'] : 1,
            'per_page'    => isset($meta['per_page']) ? (int)$meta['per_page'] : (is_array($data) ? max(1, count($data)) : 20),
            'meta'        => array_merge([
                'timestamp'  => gmdate('Y-m-d\TH:i:s\Z'),
                'lang'       => $lang,
                'request_id' => bin2hex(random_bytes(8))
            ], $meta)
        ];

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function success($data = null, $message = 'Success', $statusCode = 200, $meta = [])
    {
        self::send(true, $data, $message, $statusCode, $meta);
    }

    public static function paginated(array $rows, int $total, int $page = 1, int $perPage = 20, string $message = 'Success', int $statusCode = 200, array $extraMeta = [])
    {
        $meta = array_merge([
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'rows'     => $rows,
        ], $extraMeta);
        self::send(true, $rows, $message, $statusCode, $meta);
    }

    public static function error($message = 'Error', $statusCode = 400, $data = null)
    {
        self::send(false, $data, $message, $statusCode);
    }
}

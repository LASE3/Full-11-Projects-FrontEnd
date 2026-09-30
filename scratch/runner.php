<?php
            $_SESSION = [
                'vostok_authenticated' => true,
                'vostok_system_ADM' => true,
                'vostok_system_CRM' => true,
                'vostok_system_CUS' => true,
                'vostok_system_DEV' => true,
                'vostok_system_EMP' => true,
                'vostok_system_DOC' => true,
                'vostok_system_FIN' => true,
                'vostok_system_HR'  => true,
                'vostok_system_IT'  => true,
                'vostok_system_SHP' => true,
                'vostok_system_WEB' => true,
                'vostok_user' => [
                    'account_id' => 1,
                    'user_id' => 'EMP-0001',
                    'emp_id' => 'EMP-0001',
                    'cus_id' => 'CUS-1001',
                    'username' => 'admin',
                    'full_name' => 'System Administrator',
                    'email' => 'admin@gmail.com',
                    'role_name' => 'Executive SuperAdmin',
                    'department_code' => 'EXE',
                    'clearance_level' => 'L4',
                    'account_type' => 'Executive'
                ],
                'emp_id' => 'EMP-0001',
                'cus_id' => 'CUS-1001'
            ];
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['HTTP_HOST'] = 'localhost';
            $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
            $_SERVER['REQUEST_URI'] = '/' . escapeshellarg($argv[1]);
            ob_start();
            try {
                include $argv[1];
                $out = ob_get_clean();
                echo 'PAGE_OK';
            } catch (Throwable $e) {
                ob_end_clean();
                echo 'EXCEPTION: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
            }
        
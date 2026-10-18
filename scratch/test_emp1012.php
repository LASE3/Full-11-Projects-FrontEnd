<?php
require 'config/db.php';
$u = queryUserByCredentials('EMP-1012');
var_dump($u);
require 'includes/authorize.php';
try {
    vp_authorize($u, 'announcement:create', []);
    echo "AUTHORIZED\n";
} catch (Exception $e) {
    echo "THREW: " . $e->getMessage() . "\n";
}

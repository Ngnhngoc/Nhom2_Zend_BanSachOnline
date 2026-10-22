<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

$dsn      = getenv('DB_DSN');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

return [
    'db' => [
        'driver'   => 'Pdo_Mysql',
        'dsn'      => $dsn === false ? 'mysql:dbname=ban_sach_online;host=127.0.0.1;charset=utf8mb4' : $dsn,
        'username' => $username === false ? 'root' : $username,
        'password' => $password === false ? '' : $password,
    ],
];

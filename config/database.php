<?php
$env = parse_ini_file('.env');

return [
    'host' => $env['DB_HOST'],
    'dbname' => $env['DB_NAME'],
    'username' => $env['DB_USERNAME'],
    'password' => $env['DB_PASSWORD'],
    'charset' => $env['DB_CHARSET']
];

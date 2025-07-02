<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// config.php — настройки подключения к базе данных
define('DB_HOST', 'localhost');
define('DB_NAME', 'real_estate');
define('DB_USER', 'root');
define('DB_PASS', '');    // пустая строка, т.е. без пароля

function getPDO() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    return new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}
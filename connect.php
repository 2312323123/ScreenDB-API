<?php // included in makestuffwork.php

$host = '127.0.0.1';
$db   = 'screendb';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$collate = 'utf8mb4_general_ci';

function connect()
{
    global $host, $db, $user, $pass, $charset, $collate;

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        return $pdo;
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}
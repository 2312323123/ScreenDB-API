<?php
/* cat/pack, not image */

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'PUT' && empty($_POST))
    $_PUT = json_decode(file_get_contents('php://input'), true);
if(!isset($_PUT['name']))
    $_PUT['name'] = 'not set yet';

require_once('makestuffwork.php');
require_once 'checklogin.php';
if(!checklogin($_PUT['string'])) {
    die(0);
}

try {
    $pdo = connect();

    if(isset($_PUT['cat'])) {
        $stmt = $pdo->prepare("INSERT INTO cats (id, name)
            VALUES (?, ?)");
        $stmt->execute([$_PUT['cat'], $_PUT['name']]);
    }

    if(isset($_PUT['pack'])) {
        $stmt = $pdo->prepare("INSERT INTO packs (id, name)
            VALUES (?, ?)");
        $stmt->execute([$_PUT['pack'], $_PUT['name']]);
    }

    $pdo = null;
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
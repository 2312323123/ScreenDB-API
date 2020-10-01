<?php
/*
id -> required
cat / pack / image -> true / false

DELETE FROM imagedata
WHERE id=':id';

DELETE FROM cats
WHERE id=':id';

DELETE FROM packs
WHERE id=':id';
*/

header('Content-Type: application/json');

require_once('makestuffwork.php');


if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST))
    $_DELETE = json_decode(file_get_contents('php://input'), true);
if(!isset($_DELETE['id']))
    die();

require_once('makestuffwork.php');
require_once('checklogin.php');
if(!checklogin($_DELETE['string'])) {
    die(0);
}

try {
    $pdo = connect();

    if(isset($_DELETE['cat']) && boolval($_DELETE['cat'])) {
        $stmt = $pdo->prepare("DELETE FROM cats
            WHERE id=?");
        $stmt->execute([$_DELETE['id']]);
    }

    if(isset($_DELETE['pack']) && boolval($_DELETE['pack'])) {
        $stmt = $pdo->prepare("DELETE FROM packs
            WHERE id=?");
        $stmt->execute([$_DELETE['id']]);
    }

    if(isset($_DELETE['image']) && boolval($_DELETE['image'])) {
        $stmt = $pdo->prepare("SELECT url 
            FROM imagedata
            WHERE id=?");
        $stmt->execute([$_DELETE['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        unlink("./images/".$row['url']);
        
        $stmt = $pdo->prepare("DELETE FROM imagedata
            WHERE id=?");
        $stmt->execute([$_DELETE['id']]);
    }

    $pdo = null;
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
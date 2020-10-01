<?php

/*
id -> required
cat / pack / image -> true / false
name -> optional
newid -> optional
*/

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST))
    $_POST = json_decode(file_get_contents('php://input'), true);
if(!isset($_POST['id']))
    die();

require_once('makestuffwork.php');
require_once('checklogin.php');
if(!checklogin($_POST['string'])) {
    die(0);
}

try {
    $pdo = connect();

    if(isset($_POST['cat']) && boolval($_POST['cat'])) {
        if(isset($_POST['name'])) {
            if(isset($_POST['newid'])) {
                // name && newid
                $stmt = $pdo->prepare("UPDATE cats
                    SET name=?, id=?
                    WHERE id=?");
                $stmt->execute([$_POST['name'], $_POST['newid'], $_POST['id']]);
            } else {
                // just name
                $stmt = $pdo->prepare("UPDATE cats
                    SET name=?
                    WHERE id=?");
                $stmt->execute([$_POST['name'], $_POST['id']]);
            }
        } else if(isset($_POST['newid'])) {
            // just newid
            $stmt = $pdo->prepare("UPDATE cats
                SET id=?
                WHERE id=?");
            $stmt->execute([$_POST['newid'], $_POST['id']]);
        }
    }

    if(isset($_POST['pack']) && boolval($_POST['pack'])) {
        if(isset($_POST['name'])) {
            if(isset($_POST['newid'])) {
                // name && newid
                $stmt = $pdo->prepare("UPDATE packs
                    SET name=?, id=?
                    WHERE id=?");
                $stmt->execute([$_POST['name'], $_POST['newid'], $_POST['id']]);
            } else {
                // just name
                $stmt = $pdo->prepare("UPDATE packs
                    SET name=?
                    WHERE id=?");
                $stmt->execute([$_POST['name'], $_POST['id']]);
            }
        } else if(isset($_POST['newid'])) {
            // just newid
            $stmt = $pdo->prepare("UPDATE packs
                SET id=?
                WHERE id=?");
            $stmt->execute([$_POST['newid'], $_POST['id']]);
        }
    }

    if(isset($_POST['image']) && boolval($_POST['image'])) {
        $stmt = $pdo->prepare("UPDATE imagedata
            SET name=?
            WHERE id=?");
        $stmt->execute([$_POST['name'], $_POST['id']]);
    }

    $pdo = null;
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
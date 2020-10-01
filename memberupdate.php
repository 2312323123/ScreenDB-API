<?php
/*
id -> required
cat/pack -> true/false
parentid -> empty => NULL (cat)
         -> parentid => UPDATE (cat) / switch (pack) 
*/

// session_start();

// if(!(isset($_SESSION['loggedin']) && boolval($_SESSION['loggedin']))) {
//     die();
// }

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST))
    $_POST = json_decode(file_get_contents('php://input'), true);
if(!isset($_POST['id']))
    die();

require_once('makestuffwork.php');
require_once 'checklogin.php';
if(!checklogin($_POST['string'])) {
    die(0);
}

try {
    $pdo = connect();

    if(isset($_POST['cat']) && boolval($_POST['cat'])) {
        if(isset($_POST['parentid'])) {
            $stmt = $pdo->prepare("UPDATE catmembers
                SET catid=?
                WHERE id=?");
            $stmt->execute([$_POST['parentid'], $_POST['id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE catmembers
                SET catid=NULL
                WHERE id=?");
            $stmt->execute([$_POST['id']]);
        }
    }

    if(isset($_POST['pack']) && boolval($_POST['pack']) && isset($_POST['parentid'])) {
        $stmt = $pdo->prepare('SELECT * FROM packmembers 
            WHERE id=? AND packid=?');
        $stmt->execute([$_POST['id'], $_POST['parentid']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if( ! $row) {
            // >pack doesn't figure => add
            $stmt = $pdo->prepare("INSERT INTO packmembers
                VALUES (?, ?);");
            $stmt->execute([$_POST['id'], $_POST['parentid']]);
        } else {
            // >pack figures => remove
            $stmt = $pdo->prepare("DELETE FROM packmembers
                WHERE id=? AND packid=?;");
            $stmt->execute([$_POST['id'], $_POST['parentid']]);
        }
    }

    $pdo = null;
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
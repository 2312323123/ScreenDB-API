<?php

function addImage($id, $path, $hash, $name = false) {
    $ok;

    $category = false;
    if(isset($_POST['category']))
        $category = $_POST['category'];

    try {
        $pdo = connect();

        $stmt = $pdo->prepare("SELECT count(*) FROM `imagedata`
            WHERE hash=?");
        $stmt->execute([$hash]);
        $number_of_rows = $stmt->fetchColumn();
        if($number_of_rows == 0) {
            $ok = true;

            if($name) {
                $stmt = $pdo->prepare("
                    INSERT INTO `imagedata` (id, name, url, hash)
                    VALUES (?, ?, ?, ?);");
                $stmt->execute([$id, $name, $path, $hash]);
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO `imagedata` (id, path, hash)
                    VALUES (?, ?, ?);");
                $stmt->execute([$id, $path, $hash]);
            }

        } else
            $ok = false;

        $pdo = null;
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }

    return $ok;
}

// echo '<br/>still alive<br/>';
/*
INSERT INTO `imagedata`
VALUES ('id', 'name'/DEFAULT, 'path', DEFAULT)
*/
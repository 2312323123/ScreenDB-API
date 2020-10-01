<?php

require_once 'connect.php';

try {
    $pdo = connect();

    $stmt = $pdo->query("SELECT id, name, url, date, hash FROM `imagedata`;");
        // WHERE hash IS NULL;");
    $imagedata = $stmt->fetchAll();

    echo count($imagedata);
    echo "<br/>";
    echo "<br/>";
    echo var_dump($imagedata[0]);
    echo "<br/>";
    echo "<br/>";
    echo hash_file("md5", "https://www.jsworldcontrol.com/api/images/" . $imagedata[0]['url']);
    
    // $stmt = $pdo->prepare("UPDATE imagedata
    //     SET hash=?
    //     WHERE id=?");

    // for($i = 0; $i < count($imagedata); $i++)
    //     $stmt->execute([hash_file("md5", "https://www.jsworldcontrol.com/api/images/" . $imagedata[$i]['url']), $imagedata[$i]['id']]);
    
    // $stmt = $pdo->query("SELECT id, name, url, date FROM `imagedata` ORDER BY `date` DESC;");
    // $res = $stmt->fetchAll();

    // $final['images'] = $res;

    // if(isset($_POST['cat']) && boolval($_POST['cat'])) {
    //     if(isset($_POST['name'])) {
    //         if(isset($_POST['newid'])) {
    //             // name && newid
    //             $stmt = $pdo->prepare("UPDATE cats
    //                 SET name=?, id=?
    //                 WHERE id=?");
    //             $stmt->execute([$_POST['name'], $_POST['newid'], $_POST['id']]);
    //         } else {
    //             // just name
    //             $stmt = $pdo->prepare("UPDATE cats
    //                 SET name=?
    //                 WHERE id=?");
    //             $stmt->execute([$_POST['name'], $_POST['id']]);
    //         }
    //     } else if(isset($_POST['newid'])) {
    //         // just newid
    //         $stmt = $pdo->prepare("UPDATE cats
    //             SET id=?
    //             WHERE id=?");
    //         $stmt->execute([$_POST['newid'], $_POST['id']]);
    //     }
    // }

    // if(isset($_POST['pack']) && boolval($_POST['pack'])) {
    //     if(isset($_POST['name'])) {
    //         if(isset($_POST['newid'])) {
    //             // name && newid
    //             $stmt = $pdo->prepare("UPDATE packs
    //                 SET name=?, id=?
    //                 WHERE id=?");
    //             $stmt->execute([$_POST['name'], $_POST['newid'], $_POST['id']]);
    //         } else {
    //             // just name
    //             $stmt = $pdo->prepare("UPDATE packs
    //                 SET name=?
    //                 WHERE id=?");
    //             $stmt->execute([$_POST['name'], $_POST['id']]);
    //         }
    //     } else if(isset($_POST['newid'])) {
    //         // just newid
    //         $stmt = $pdo->prepare("UPDATE packs
    //             SET id=?
    //             WHERE id=?");
    //         $stmt->execute([$_POST['newid'], $_POST['id']]);
    //     }
    // }

    // if(isset($_POST['image']) && boolval($_POST['image'])) {
    //     $stmt = $pdo->prepare("UPDATE imagedata
    //         SET name=?
    //         WHERE id=?");
    //     $stmt->execute([$_POST['name'], $_POST['id']]);
    // }

    $pdo = null;
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
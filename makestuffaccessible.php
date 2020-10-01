<?php
// print_r(password_hash('hehe', PASSWORD_BCRYPT));
// print_r(password_verify('hehe', '$2y$10$OUT71nvxWmY0Hc8FayV82uJJrlCPJBHO3a1fi659CLli0oe.AS7zS'));

    // session_start();

    // if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    //     echo json_encode(array('response' => 'already logged in'));
    //     die(0);
    // }

    // header('Content-Type: application/json');

    if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST))
    $_POST = json_decode(file_get_contents('php://input'), true);
    if(!isset($_POST['string'])) {
        echo json_encode(array('response' => 'enter password maybe'));
        die(0);
    }

    require_once('makestuffwork.php');

    try {
    $pdo = connect();
    if(true) {
        $stmt = $pdo->query("SELECT * FROM pass");
        $res = $stmt->fetchAll();
        for($i = 0; $i < count($res); $i++) {
            if(password_verify($_POST['string'], $res[$i]['pass'])) { //  . "41220cb4326079f231ac3ca5a0389da3"
                // $_SESSION['loggedin'] = true;
                echo json_encode(array('response' => 'correct password, feel free to do stuff'));
                die(0);
            }
        }
    }
    echo json_encode(array('response' => 'wrong password'));

    
    // $stmt = $pdo->prepare("SELECT imagedata.id, name, url, date
    // FROM  imagedata
    // INNER JOIN catmembers
    // ON catmembers.id = imagedata.id
    // WHERE catid = ?;");
    // $stmt->execute([$categories[$i-2]['id']]);
    // $final['categories'][$i]['images'] = $stmt->fetchAll();



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
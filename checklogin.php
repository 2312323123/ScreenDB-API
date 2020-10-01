<?php


// if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST))
// $_POST = json_decode(file_get_contents('php://input'), true);
// if(!isset($_POST['string'])) {
//     echo json_encode(array('response' => 'enter password maybe'));
//     die(0);
// }


function checklogin($p)
{
    require_once('makestuffwork.php');

    try {
        $pdo = connect();
        if(true) {
            $stmt = $pdo->query("SELECT * FROM pass");
            $res = $stmt->fetchAll();
            for($i = 0; $i < count($res); $i++) {
                if(password_verify($p, $res[$i]['pass'])) {
                    return true;
                }
            }
        }
        return false;

        $pdo = null;
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}
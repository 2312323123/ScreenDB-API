<?php
/*
1) imagedata:
INSERT INTO `imagedata`
VALUES ('id', 'name'/DEFAULT, 'path', DEFAULT)

UPDATE: WHERE id=':id'

DELETE FROM imagedata
WHERE id=':id';


2) cats:
INSERT INTO cats
VALUES ('id', 'name'/DEFAULT, DEFAULT)");

UPDATE cats
SET id=':id'
WHERE id=':id';

DELETE FROM cats
WHERE id=':id';


3) catmembers:
UPDATE catmembers
SET catid=':catid'
WHERE id=':id';


4) packs:
INSERT INTO packs
VALUES ('id', 'name'/DEFAULT, DEFAULT)");

UPDATE packs
SET id=':id'
WHERE id=':id';

DELETE FROM packs
WHERE id=':id';


5) packmembers:
INSERT INTO packmembers
VALUES (':id', ':packid')

// UPDATE catmembers
// SET catid=':catid'
// WHERE id=':id';

DELETE FROM packmembers
WHERE id=':id' AND packid=':packid'
*/

require_once 'connect.php';

if (!file_exists('./images/')) {
    mkdir('./images/', 0777, true);
    copy('./unspecified.png', './images/unspecified.png');
    copy('./all.jpg', './images/all.jpg');
}
if (!file_exists('./tmp/')) {
    mkdir('./tmp/', 0777, true);
}

function check() {
    global $host, $db, $user, $pass, $charset, $collate;
    
    $dsn = "mysql:host=$host;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        // $stmt = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'");
        $stmt = $pdo->query("SHOW DATABASES LIKE '$db';");
        $pdo = null;
        return (bool) $stmt->fetchColumn();
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}
/*
$host = '127.0.0.1';
$db   = 'ScreenDB';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$collate = 'utf8mb4_general_ci';
*/

if(!check()) { // do something if db doesn't exist
    try {
    $conn = new PDO("mysql:host=$host", $user, $pass);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE DATABASE $db CHARACTER SET $charset COLLATE $collate";
    // use exec() because no results are returned
    $conn->exec($sql);
    echo "Database created successfully<br>";
    } catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
    }

    $conn = null;

    /* now we should have db */
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
    ];
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        /* IMAGEDATA */
        $pdo->exec("CREATE TABLE `imagedata` (
            `id` varchar(6) NOT NULL,
            `name` varchar(255) NOT NULL DEFAULT 'no name yet',
            `url` varchar(255) NOT NULL,
            `date` bigint,
            `hash` varchar(255) NOT NULL,
            PRIMARY KEY (`id`))");

        $pdo->exec("CREATE TRIGGER before_image_insert_date 
            BEFORE INSERT 
            ON imagedata FOR EACH ROW 
            SET new.date = UNIX_TIMESTAMP(NOW());");

        /* CATEGORIES */
        $pdo->exec("CREATE TABLE `cats` (
            `id` varchar(6) NOT NULL,
            `name` varchar(255) NOT NULL DEFAULT 'no name yet',
            `date` bigint NOT NULL,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`id`) REFERENCES `imagedata`(`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE)");

        $pdo->exec("CREATE TABLE `catmembers` (
            `id` varchar(6) NOT NULL,
            `catid` varchar(6),
            FOREIGN KEY (`id`) REFERENCES `imagedata`(`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            FOREIGN KEY (`catid`) REFERENCES `cats`(`id`)
            ON DELETE SET NULL
            ON UPDATE CASCADE)");
            
        $pdo->exec("CREATE TRIGGER before_image_insert_category
            AFTER INSERT 
            ON imagedata FOR EACH ROW
            INSERT INTO catmembers
            VALUES (new.id, NULL)");

        $pdo->exec("CREATE TRIGGER before_cats_insert_date 
            BEFORE INSERT 
            ON cats FOR EACH ROW 
            SET new.date = UNIX_TIMESTAMP(NOW());");
            
        /* PACKS */
        $pdo->exec("CREATE TABLE `packs` (
            `id` varchar(6) NOT NULL,
            `name` varchar(255) NOT NULL DEFAULT 'no name yet',
            `date` bigint NOT NULL,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`id`) REFERENCES `imagedata`(`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE)");

        $pdo->exec("CREATE TABLE `packmembers` (
            `id` varchar(6) NOT NULL,
            `packid` varchar(6),
            FOREIGN KEY (`id`) REFERENCES `imagedata`(`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            FOREIGN KEY (`packid`) REFERENCES `packs`(`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE)");

        $pdo->exec("CREATE TRIGGER before_packs_insert_date 
            BEFORE INSERT 
            ON packs FOR EACH ROW 
            SET new.date = UNIX_TIMESTAMP(NOW());");

        /* VIEWS */
        $pdo->exec("CREATE VIEW catsView
            AS 
            SELECT 
                cats.id,
                cats.name, 
                imagedata.url,
                cats.date
            FROM
                cats
            INNER JOIN 
                imagedata USING (id)
            ORDER BY cats.date DESC;");
            
        $pdo->exec("CREATE VIEW packsView
            AS 
            SELECT 
                packs.id,
                packs.name, 
                imagedata.url,
                packs.date
            FROM
                packs
            INNER JOIN 
                imagedata USING (id)
            ORDER BY packs.date DESC;");

        /* PASS */
        $pdo->exec("CREATE TABLE `pass` (
            `name` varchar(1023),
            `pass` varchar(1023) NOT NULL)");
        

        $pdo = null;
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }// DROP DATABASE screendb
}

// $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
// $options = [
//     PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
//     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//     PDO::ATTR_EMULATE_PREPARES   => false,
// ];
// try {
//     $pdo = new PDO($dsn, $user, $pass, $options);        

//     $pdo->query("INSERT INTO `imagedata`
//         VALUES ('hehe', 'ojejname', '/hehe.jpg', 12345)");
//     $pdo->query("INSERT INTO `imagedata`
//         VALUES ('hehe1', 'ojejname', '/hehe.jpg', DEFAULT)");
//     $pdo->query("INSERT INTO `imagedata`
//         VALUES ('hehe2', 'ojejname', '/hehe.jpg', DEFAULT)");
//     $pdo->query("INSERT INTO `imagedata`
//         VALUES ('hehe3', 'ojejname', '/hehe.jpg', DEFAULT)");
//     $pdo->query("INSERT INTO `imagedata`
//         VALUES ('hehe5', 'ojejname', '/hehe.jpg', DEFAULT)");
        
//     $pdo->query("INSERT INTO cats
//         VALUES ('hehe1', DEFAULT, DEFAULT)");
        
//     $pdo->query("UPDATE catmembers
//         SET catid='hehe1'
//         WHERE id='hehe3';");
//     $pdo->query("UPDATE cats
//         SET id='hehe2'
//         WHERE id='hehe1';");

//     $pdo = null;
// } catch (\PDOException $e) {
//     throw new \PDOException($e->getMessage(), (int)$e->getCode());
// }
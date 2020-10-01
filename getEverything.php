<?php

if (!(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || 
   $_SERVER['HTTPS'] == 1) ||  
   isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&   
   $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
{
   $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
   header('HTTP/1.1 301 Moved Permanently');
   header('Location: ' . $redirect);
   exit();
}

header('Content-Type: application/json');

require_once('makestuffwork.php');

try {
    $pdo = connect();

    /* images */
    $stmt = $pdo->query("SELECT id, name, url, date FROM `imagedata` ORDER BY `date` DESC;");
    $res = $stmt->fetchAll();

    $final['images'] = $res;

    /* categories */
    $stmt = $pdo->query("SELECT 
    cats.id,
    cats.name, 
    imagedata.url,
    cats.date
FROM
    cats
INNER JOIN 
    imagedata USING (id)
ORDER BY cats.date DESC;");
    $categories = $stmt->fetchAll();

    // not categorised and all goes here
    $final['categories'] = [
        array("id"=>"all", "name"=>"all", "url"=>"all.jpg", "date"=>"2147360620"),
        array("id"=>"nope", "name"=>"unspecified", "url"=>"unspecified.png", "date"=>"2147360620")
    ];
    
    $stmt = $pdo->query("SELECT imagedata.id, name, url, date
        FROM  imagedata
        INNER JOIN catmembers
        ON catmembers.id = imagedata.id;");
    $final['categories'][0]['images'] = $stmt->fetchAll();
    
    $stmt = $pdo->query("SELECT imagedata.id, name, url, date
        FROM  imagedata
        INNER JOIN catmembers
        ON catmembers.id = imagedata.id
        WHERE catid IS NULL;");
    $final['categories'][1]['images'] = $stmt->fetchAll();

    $final['categories'] = array_merge($final['categories'], $categories);


    for ($i = 2; $i <= count($categories)+1; $i++) {
        $stmt = $pdo->prepare("SELECT imagedata.id, name, url, date
            FROM  imagedata
            INNER JOIN catmembers
            ON catmembers.id = imagedata.id
            WHERE catid = ?;");
        $stmt->execute([$categories[$i-2]['id']]);
        $final['categories'][$i]['images'] = $stmt->fetchAll();
    }

    /* packs */
    /* TODO add view, and add getting packs */

    $stmt = $pdo->query("SELECT 
        packs.id,
        packs.name, 
        imagedata.url,
        packs.date
    FROM
        packs
    INNER JOIN 
        imagedata USING (id)
    ORDER BY packs.date DESC;");
    $packs = $stmt->fetchAll();

    $final['packs'] = $packs;

    for ($i = 0; $i < count($packs); $i++) {
        $stmt = $pdo->prepare("SELECT imagedata.id, name, url, date
            FROM  imagedata
            INNER JOIN packmembers
            ON packmembers.id = imagedata.id
            WHERE packid = ?;");
        $stmt->execute([$packs[$i]['id']]);
        $final['packs'][$i]['images'] = $stmt->fetchAll();
    }


    echo json_encode($final);

    $pdo = null;
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
<?php
// header('Content-Type: application/json');
// echo substr(base64_encode(sha1(mt_rand())), 0, 5) . "<br/>";

require 'makestuffwork.php';
require_once 'addImage.php';
require 'getnames.php';
require_once('checklogin.php');
if(!checklogin($_POST['string'])) {
    die(0);
}

/* URL INPUT */

if(isset($_POST['url'])) {
    $url_to_image = $_POST['url'];
    if($url_to_image != '') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_to_image);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $output = curl_exec($ch);
        curl_close($ch);

        if(getimagesizefromstring($output)) { // weird function is this
            $my_save_dir = './images/';
            $oldname = pathinfo(basename($url_to_image))['filename'];
            $id = generateNewName();
            $filename = $id . "." . mime2ext(getimagesizefromstring($output)['mime']);//strtolower(pathinfo($url_to_image)['extension']); // pathinfo(basename($url_to_image) ,PATHINFO_EXTENSION)
            $tmp_loc = "./tmp/$filename";
            $complete_save_loc = $my_save_dir.$filename;
            file_put_contents($tmp_loc,$output);

            $hash = hash_file ( "md5" , $tmp_loc );
            // if(notYetUploaded($tmp_loc)) {
                if(addImage($id, $filename, $hash, $oldname)) {
                    file_put_contents($complete_save_loc,$output);//file_get_contents($url_to_image));
                    echo "<br/>The file $oldname (from url) has been uploaded as $filename";
                }
            // }
            unlink($tmp_loc);
        }
    }
}

/* FILE INPUT*/
$target_dir = "./images/"; // defined in notYesUploaded as well
$fullNames; // with extensions
$names; // just names
$existsFlag = false;

if(isset($_FILES["fileToUpload"]["name"][0]) && $_FILES["fileToUpload"]["name"][0] != '')
    for($i = 0; $i < count($_FILES["fileToUpload"]["name"]); $i++) {
        // $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"][$i]);

        $newname = generateNewName();
        $target_filename = basename($newname . "." . strtolower(pathinfo($_FILES["fileToUpload"]["name"][$i],PATHINFO_EXTENSION)));
        $hash = hash_file ( "md5" , $_FILES["fileToUpload"]["tmp_name"][$i] );
        $oldname = pathinfo($_FILES["fileToUpload"]["name"][$i])['filename'];
        if(!addImage($newname, $target_filename, $hash, $oldname))
            continue;
        // if(!notYetUploaded($_FILES["fileToUpload"]["tmp_name"][$i], $i))
        //     continue;

        $target_file = $target_dir . $target_filename;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        $uploadOk = checkIfFileOK($_FILES["fileToUpload"]["tmp_name"][$i], $target_file,  $imageFileType, $i);

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file)) {
            echo "<br/>The file ". basename( $_FILES["fileToUpload"]["name"][$i]). " has been uploaded as $target_filename";
            } else {
            echo "Sorry, there was an error uploading your file.";
            }
        }
    }






                    // file,   path,         png i.e.,       $i/nothing
function checkIfFileOK($file, $target_file, $imageFileType, $identifier) {
    $check = getimagesize($file);
    if($check !== false) {
        // echo "<br/>File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "<br/>File is not an image.";
        $uploadOk = 0;
    }

        
    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "svg" 
    && $imageFileType != "gif" && $imageFileType != "bmp" && $imageFileType != "tiff" && $imageFileType != "webp" ) {
        echo "Sorry, only JPG, JPEG, PNG, SVG, BMP, TIFF, GIF & WEBP files are allowed.";
        $uploadOk = 0;
    }

    return $uploadOk;
}

?>
        
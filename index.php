<?php

header("Content-Type: application/json");
header("Acess-Control-Allow-Methods: POST");
header("Acess-Control-Allow-Headers: Acess-Control-Allow-Headers,Content-Type,Acess-Control-Allow-Methods, Authorization");

$data = json_decode(file_get_contents("php://input"), true); // collect input parameters and convert into readable format

$fileName  =  $_FILES['sendimage']['name'];
$tempPath  =  $_FILES['sendimage']['tmp_name'];
$fileSize  =  $_FILES['sendimage']['size'];

// $txt = "user id date"; $myfile = file_put_contents('logs.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);

function get_client_ip()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

if (get_client_ip() != "151.106.118.116" && get_client_ip() != "93.188.161.201") {
    $errorMSG = json_encode(array("message" => 'not allowed', "status" => false));
    echo $errorMSG;
} else {
    if (empty($fileName)) {
        $errorMSG = json_encode(array("message" => "image not found", "status" => false));
        echo $errorMSG;
    } else {
        $upload_path = 'media/' . $_POST['path']; // set upload folder path 
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // get image extension

        // valid image extensions
        $valid_extensions = array('jpeg', 'webp', 'jpg', 'png', 'gif', 'mp4');

        // allow valid image file formats
        if (in_array($fileExt, $valid_extensions)) {
            //check file not exist our upload folder path
            if (!file_exists($upload_path . $fileName)) {
                move_uploaded_file($tempPath, $upload_path . $fileName); // move file from system temporary path to our upload folder path 
            }
        } else {
            $errorMSG = json_encode(array("message" => "Sorry, only JPG, WEBP, JPEG, PNG & GIF files are allowed", "status" => false));
            echo $errorMSG;
        }
    }

    // if no error caused, continue ....
    if (!isset($errorMSG)) {
        echo json_encode(array("message" => "Image Uploaded Successfully", "status" => true, 'ip' => get_client_ip() ?? null));
    }
}

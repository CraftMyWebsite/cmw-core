<?php
$response = "error";
if(isset($_FILES['file']['name'])){
    $filename = $_FILES['file']['name'];
    $path_parts = pathinfo($filename);

    $imageFileType = strtolower($path_parts['extension']);
    $imageName = strtolower($path_parts['filename']);

    $filename = $imageName."-".uniqid().".".$imageFileType;

    $location = $_SERVER['DOCUMENT_ROOT'] ."/public/uploads/".$filename;


    $valid_extensions = array("jpg","jpeg","png");

    if(in_array($imageFileType, $valid_extensions)) {
        if(move_uploaded_file($_FILES['file']['tmp_name'],$location)){
            $response = $filename;
        }
    }
}

echo json_encode($response);
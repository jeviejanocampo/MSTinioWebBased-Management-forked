<?php
header('Content-Type: application/json');
include 'dbcon.php';  

file_put_contents('upload_log.txt', print_r($_FILES, true)); 

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image'])) { 
        $targetDir = "C:/xampp/htdocs/capstone-template/gcash_receipts/"; 
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            echo json_encode(['success' => false, 'message' => 'File is not an image.']);
            exit;
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            echo json_encode(['success' => true, 'message' => 'File uploaded successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sorry, there was an error uploading your file.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>

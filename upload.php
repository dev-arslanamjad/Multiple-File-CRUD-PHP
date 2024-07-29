<?php
include("dbcon.php");
session_start();

// Create the uploads directory if it doesn't exist
$targetDir = "uploads/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Initialize variables
$profilePictureName = "";
$documentNames = [];

// Handle profile picture upload
if (isset($_FILES['profilepicture']) && $_FILES['profilepicture']['error'] == UPLOAD_ERR_OK) {
    $profileExtension = pathinfo($_FILES['profilepicture']['name'], PATHINFO_EXTENSION);
    $profilePictureName = "dp" . rand(1, 999) . time() . "." . $profileExtension;
    $targetFilePath = $targetDir . $profilePictureName;

    if (!move_uploaded_file($_FILES['profilepicture']['tmp_name'], $targetFilePath)) {
        $_SESSION['status'] = 'Error uploading profile picture.';
        header('Location: index.php');
        exit();
    }
}

// Handle document uploads
if (isset($_FILES['documents']) && is_array($_FILES['documents']['tmp_name'])) {
    foreach ($_FILES['documents']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['documents']['error'][$key] == UPLOAD_ERR_OK) {
            $fileName = basename($_FILES['documents']['name'][$key]);
            $targetFilePath = $targetDir . $fileName;

            if (move_uploaded_file($tmp_name, $targetFilePath)) {
                $documentNames[] = $fileName;
            } else {
                echo "Error uploading file $fileName.<br>";
            }
        } else {
            echo "Error code: " . $_FILES['documents']['error'][$key] . "<br>";
        }
    }
} else {
    echo "No documents were uploaded or invalid file input.";
}

// Insert data into database
if ($profilePictureName != "" || !empty($documentNames)) {
    $username = $_POST['username'];
    $rollno = $_POST['rollno'];
    $documents = implode(",", $documentNames); // Convert array to string

    $stmt = $conn->prepare("INSERT INTO images (name, rollno, profile, result) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $rollno, $profilePictureName, $documents);

    if ($stmt->execute()) {
        $_SESSION['status'] = 'Successfully Added!';
    } else {
        $_SESSION['status'] = 'Failed to upload profile picture and documents.';
    }

    $stmt->close();
} else {
    $_SESSION['status'] = 'No files were uploaded.';
}

$conn->close();
header('Location: index.php');
exit();
?>

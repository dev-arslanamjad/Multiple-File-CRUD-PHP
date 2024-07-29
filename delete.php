<?php
include("dbcon.php");
session_start();

// Check if the ID parameter is set
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM `images` WHERE `ID` = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['status'] = " Deleted successfully!";
            header("Location: view.php");
            exit();
        } else {
            $_SESSION['status'] = "Error deleting record: " . $conn->error;
            header("Location: view.php");
            exit();
        }
    } else {
        $_SESSION['status'] = "Error preparing the query: " . $conn->error;
        header("Location: view.php");
        exit();
    }
} else {
    $_SESSION['status'] = "No ID specified!";
    header("Location: view.php");
    exit();
}

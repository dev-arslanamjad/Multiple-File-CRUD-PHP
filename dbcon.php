<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "imgupload";
$conn = new mysqli($servername, $username, $password, $database);
if (!$conn) {
  die("Connection Error". mysqli_error($conn));
}
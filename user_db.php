<?php

$host = "localhost";
$username = "root";
$password = ""; 
$dbname = "user_db";

$conn = null;

try {
    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    die("Error connecting to the database: " . $e->getMessage());
}
?>
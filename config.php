<?php

$host = 'localhost';
$dbname = 'user_db';
$username = 'root';
$password = '';

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Error connecting to the database: " . $e->getMessage());
}

?>
<?php
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "user_db";

$conn = new mysqli($servername, $username, $password, $dbname);
$sql = "SELECT id, name, email, role FROM users";
$result = $conn->query($sql);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Database connection object is available.<br>";
}

$conn->set_charset("utf8");
?>
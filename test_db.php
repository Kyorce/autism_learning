<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "<br>");
}
echo "Successfully connected to the database!<br>";

$conn->close();
?>
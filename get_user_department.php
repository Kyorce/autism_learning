<?php
session_start();
require_once 'user_db.php'; 

if (!isset($_SESSION['email'])) {
    
    http_response_code(401);
    echo json_encode(['department' => 'Not Logged In']);
    exit();
}

$user_email = $_SESSION['email'];

$sql_user = "SELECT d.name AS department_name
             FROM user u
             LEFT JOIN departments d ON u.department_id = d.id
             WHERE u.email = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $user_email);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_data = $result_user->fetch_assoc();
$stmt_user->close();

$department_name = isset($user_data['department_name']) ? htmlspecialchars($user_data['department_name']) : 'Not Assigned';


header('Content-Type: application/json');
echo json_encode(['department' => $department_name]);
?>
<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'config.php';

date_default_timezone_set('Asia/Manila'); 

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("SELECT email FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['register_error'] = 'Email is already registered!';
        $_SESSION['active_form'] = 'register';
    } else {
        $insertStmt = $conn->prepare("INSERT INTO user (name, email, password, role) VALUES (?, ?, ?, ?)");
        $insertStmt->bind_param("ssss", $name, $email, $password, $role);
        if ($insertStmt->execute()) {
            $_SESSION['register_success'] = 'Registration successful! You can now log in.';
        } else {
            $_SESSION['register_error'] = 'Registration failed. Please try again.';
            $_SESSION['active_form'] = 'register';
        }
        $insertStmt->close();
    }
    $stmt->close();
    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

         
            $ipAddress = $_SERVER['REMOTE_ADDR'];
           
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $userId = $_SESSION['user_id'];
            $loginTime = date('Y-m-d H:i:s'); 
            $historyStmt = $conn->prepare("INSERT INTO login_history (user_id, ip_address, user_agent, login_timestamp) VALUES (?, ?, ?, ?)");
            if ($historyStmt) {
                $historyStmt->bind_param("isss", $userId, $ipAddress, $userAgent, $loginTime);
                $historyStmt->execute();
                $historyStmt->close();
            } else {
                error_log("Error preparing SQL for login history: " . $conn->error);
               
            }

            $_SESSION['last_login_details'] = [
                'time' => $loginTime,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ];

            if ($_SESSION['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: user.php");
            }
            exit();
        } else {
            $_SESSION['login_error'] = 'Incorrect email or password';
            $_SESSION['active_form'] = 'login';
            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = 'Incorrect email or password';
        $_SESSION['active_form'] = 'login';
        header("Location: index.php");
        exit();
    }
}
?>
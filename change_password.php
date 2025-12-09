<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

require_once 'user_db.php';     

if (isset($_POST['change_password'])) {
    $email = $_SESSION['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $error = '';
    $success = '';


    $sql = "SELECT password FROM user WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_current_password_db = $row['password'];

        if (password_verify($current_password, $hashed_current_password_db)) {
            if ($new_password === $confirm_password) {
                if (strlen($new_password) >= 6) {
                    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

                    $update_sql = "UPDATE user SET password = '$hashed_new_password' WHERE email = '$email'";
                    if ($conn->query($update_sql) === TRUE) {
                        $success = "Password changed successfully!";
                    } else {
                        $error = "Error updating password: " . $conn->error;
                    }
                } else {
                    $error = "New password must be at least 6 characters long.";
                }
            } else {
                $error = "New password and confirm password do not match.";
            }
        } else {
            $error = "Incorrect current password.";
        }
    } else {
        $error = "User not found.";
    }

    $_SESSION['password_change_message'] = $success ? ['type' => 'success', 'text' => $success] : ($error ? ['type' => 'error', 'text' => $error] : null);
    header("Location: settings.php");
    exit();
} else {
    header("Location: settings.php");
    exit();
}


if (isset($conn)) {
    $conn->close();
}
?>
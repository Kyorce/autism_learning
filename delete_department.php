<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once 'user_db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_to_delete = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    $sql_delete = "DELETE FROM departments WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);

    if ($stmt_delete) {
        $stmt_delete->bind_param("i", $id_to_delete);

        if ($stmt_delete->execute()) {
            $_SESSION['message'] = '<div class="success-message">Department deleted successfully!</div>';
        } else {
            $_SESSION['message'] = '<div class="error-message">Error deleting department: ' . $stmt_delete->error . '</div>';
        }

        $stmt_delete->close();
    } else {
        $_SESSION['message'] = '<div czass="error-message">Error preparing SQL statement for deletion: ' . $conn->error . '</div>';
    }
} else {
    $_SESSION['message'] = '<div class="error-message">Invalid department ID.</div>';
}

header("Location: departments.php");
exit();
?>
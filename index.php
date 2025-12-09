<?php
session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? '',
    'register_success' => $_SESSION['register_success'] ?? ''
];

$activeForm = $_SESSION['active_form'] ?? 'login';

unset($_SESSION['login_error']);
unset($_SESSION['register_error']);
unset($_SESSION['active_form']);
unset($_SESSION['register_success']);

function showError(string $error): string
{
    return !empty($error) ? "<div class='error-message'>$error</div>" : '';
}

function showSuccess(string $success): string
{
    return !empty($success) ? "<div class='success-message'>$success</div>" : '';
}

function isActiveForm(string $formName, string $activeForm): string
{
    return $formName === $activeForm ? 'active' : '';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FACULTY MANAGEMENT SYSTEM</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">
<?= showSuccess(success: $errors['register_success']); ?>
<div class="form-box <?= isActiveForm(formName: 'login', activeForm: $activeForm); ?>" id="loginForm">
    <form action="login_register.php" method="post">
        <h2>Login</h2>
        <?= showError(error: $errors['login']); ?>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
        <p>Don't have an account? <a href="#" onclick="showForm('registerForm')">Register</a></p>
    </form>
</div>

    <div class="form-box <?= isActiveForm(formName: 'register', activeForm: $activeForm); ?>" id="registerForm">
        <form action="login_register.php" method="post">
            <h2>Register</h2>
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="">--Select Role--</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" name="register">Register</button>
            <p>Already have an account? <a href="#" onclick="showForm('loginForm')">Login</a></p>
        </form>
    </div>
</div>

<script src="script.js"></script>
<script>
    function showForm(formId) {
        document.querySelectorAll(".form-box").forEach(form => form.classList.remove("active"));
        const targetForm = document.getElementById(formId);
        if (targetForm) {
            targetForm.classList.add("active");
        } else {
            console.error(`Form with ID '${formId}' not found.`);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        <?php if ($activeForm === 'login'): ?>
            showForm('loginForm');
        <?php elseif ($activeForm === 'register'): ?>
            showForm('registerForm');
        <?php endif; ?>
    });
</script>
</body>

</html>
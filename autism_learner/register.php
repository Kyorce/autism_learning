<?php
include 'db_connect.php';

$message = '';
$error = ''; // To show registration errors

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Basic validation and checking for existing user (optional but recommended)
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $error = "Username or Email already exists. Please choose a different one.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt_insert = $conn->prepare("INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
        
        // Using username for full_name as a simple default
        $stmt_insert->bind_param("ssss", $username, $email, $hashed_password, $username);
        
        if ($stmt_insert->execute()) {
            $message = "Registration successful! You can now log in.";
        } else {
            $error = "Error: " . $stmt_insert->error;
        }
        $stmt_insert->close();
    }
    $stmt_check->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Learner Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="landing-page-body">

    <!-- Navbar (Same as index.php) -->
    <header class="navbar">
        <div class="logo">
            <span class="logo-icon">🌈</span>
            AUTISM LEARNING
        </div>
        <nav class="nav-links">
            <a href="index.php#home">HOME</a>
            <a href="index.php#about">ABOUT US</a>
            <a href="index.php#services">SERVICES</a>
            <a href="index.php#contact">CONTACT US</a>
        </nav>
        <div class="auth-buttons">
            <a href="register.php" class="btn btn-register current-page">REGISTER</a>
            <button onclick="window.location.href='index.php'" class="btn btn-log-in">LOG IN</button>
        </div>
    </header>

    <!-- Registration Form Container -->
    <div class="register-container">
        <form method="post" action="register.php" class="registration-form">
            <h2 class="form-title">Learner Registration</h2>
            
            <?php if ($message): ?>
                <p class="success-message"><?php echo $message; ?></p>
            <?php elseif ($error): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>

            <label for="reg-username">Username:</label>
            <input type="text" id="reg-username" name="username" required>
            
            <label for="reg-email">Email:</label>
            <input type="email" id="reg-email" name="email" required>
            
            <label for="reg-password">Password:</label>
            <input type="password" id="reg-password" name="password" required>

            <input type="submit" value="REGISTER">
            <p class="login-link">
                Already have an account? <a href="index.php">Log in here</a>
            </p>
        </form>
    </div>

</body>
</html>
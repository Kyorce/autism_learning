<?php
include 'db_connect.php';

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$login_attempted = false; // Flag to check if form was submitted

// Handle Login Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_submit'])) {
    $login_attempted = true;
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Retrieve user data
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
    $stmt->close();
}

// Check if the login form should be displayed (e.g., if there's an error or if the login button was clicked)
// For simplicity on the landing page, we will show the login form as a modal or dedicated section
// if an error occurred during submission, otherwise it's triggered by a button.
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome to the Autism Learning App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="landing-page-body">

    <!-- Navbar -->
    <header class="navbar">
        <div class="logo">
            <span class="logo-icon">🌈</span>
            Autism Leaning
        </div>
        <nav class="nav-links">
            <a href="#home">HOME</a>
            <a href="#about">ABOUT US</a>
            <a href="#services">SERVICES</a>
            <a href="#contact">CONTACT US</a>
        </nav>
        <div class="auth-buttons">
            <a href="register.php" class="btn btn-register">REGISTER</a>
            <!-- Button to open the login modal/section -->
            <button id="loginButton" class="btn btn-log-in">LOG IN</button>
        </div>
    </header>

    <!-- Main Hero Section -->
    <main class="hero-section">
        <div class="hero-content">
            <div class="text-container">
                <h1 class="welcome-text">WELCOME TO</h1>
                <h2 class="school-name">Autism Learning</h2>
                <p class="tagline">
                    Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt.
                </p>
                <a href="#services" class="btn-more-info">MORE INFO</a>
            </div>

            <!-- Decorative Elements (for visual effect, mimicking the image) -->
            <div class="decorative-elements">
                <!-- Paper plane -->
                <div class="deco-plane"></div>
                <!-- Dotted line -->
                <div class="deco-line"></div>
                <!-- Pink square -->
                <div class="deco-square"></div>
                <!-- Yellow star -->
                <div class="deco-star"></div>
                <!-- Magnifying glass -->
                <div class="deco-magnifier"></div>
                <!-- Ruler -->
                <div class="deco-ruler"></div>
                <!-- Crayon/Pencil 1 -->
                <div class="deco-crayon-1"></div>
                <!-- Pencil 2 -->
                <div class="deco-pencil-2"></div>
                <!-- Notebook -->
                <div class="deco-notebook"></div>
                <!-- Orange circle -->
                <div class="deco-circle"></div>
                <!-- Kid's area image placeholder -->
                <div class="hero-image-placeholder">
                    <!-- This div serves as the background image container -->
                </div>
            </div>
        </div>
    </main>

    <!-- Login Modal (Hidden by default) -->
    <div id="loginModal" class="modal-overlay" style="<?php echo $error ? 'display: flex;' : 'display: none;'; ?>">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <form method="post" action="index.php" class="login-form">
                <h2>Learner Login</h2>
                <?php if ($error): ?>
                    <p class="error-message"><?php echo $error; ?></p>
                <?php endif; ?>

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <input type="submit" name="login_submit" value="LOG IN">
                <p class="register-link">
                    Don't have an account? <a href="register.php">Register here</a>
                </p>
            </form>
        </div>
    </div>


    <script>
        // Modal logic for Login
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('loginModal');
            const loginButton = document.getElementById('loginButton');
            const closeBtn = document.querySelector('.close-btn');
            const registerLink = document.querySelector('.register-link a');
            const error = document.querySelector('.error-message');

            // Show modal when login button is clicked
            if (loginButton) {
                loginButton.onclick = function() {
                    modal.style.display = 'flex';
                }
            }
            
            // Hide modal when close button is clicked
            if (closeBtn) {
                closeBtn.onclick = function() {
                    modal.style.display = 'none';
                }
            }

            // Hide modal when clicking outside the modal content
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }

            // If an error occurred during login attempt, ensure the modal stays open
            <?php if ($error): ?>
                modal.style.display = 'flex';
            <?php endif; ?>
        });
    </script>
</body>
</html>
<?php

include 'db_connect.php';


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$login_attempted = false; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_submit'])) {
    $login_attempted = true;
    $username = $_POST['username'];
    $password = $_POST['password'];
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

       
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome to the Autism Learning App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="landing-page-body">

    <header class="navbar">
        <div class="logo">
            <span class="logo-icon">🌈</span>
            Autism Learning
        </div>
        <nav class="nav-links">
            <a href="#home">HOME</a>
            <a href="#about">ABOUT US</a>
            <a href="#services">SERVICES</a>
            <a href="#contact">CONTACT US</a>
        </nav>
        <div class="auth-buttons">
            <a href="register.php" class="btn btn-register">REGISTER</a>
            <button id="loginButton" class="btn btn-log-in">LOG IN</button>
        </div>
    </header>

    <section id="home" class="hero-section content-section"> 
        <div class="hero-content">
            <div class="text-container">
                <h1 class="welcome-text">WELCOME TO</h1>
                <h2 class="school-name">Autism Learning</h2>
                <p class="tagline">
                Where potential blooms into joyful discovery.
                </p>
                <a href="#services" class="btn-more-info">MORE INFO</a>
            </div>

            <div class="decorative-elements">
                <div class="deco-plane"></div>
                <div class="deco-line"></div>
                <div class="deco-square"></div>
                <div class="deco-star"></div>
       
                <div class="deco-notebook"></div>
                <div class="deco-circle"></div>
                <div class="hero-image-placeholder"></div>
            </div>
        </div>
    </section>
    
    <section id="about" class="about-section content-section">
        <h2>About Us 🌟</h2>
        <p>Learn about our mission, vision, and the dedicated team behind the Autism Learning App.</p>
        <div style="height: 300px; background-color: rgba(255, 255, 255, 0.5); padding: 20px; border-radius: 10px;">
            <p>Our goal is to create a safe, engaging, and effective learning environment for children on the autism spectrum. We focus on personalized, interactive lessons.</p>
        </div>
    </section>

    <section id="services" class="services-section content-section">
        <h2>Our Services 📚</h2>
        <p>Explore the educational programs, interactive games, and support features we offer for learners and parents.</p>
        <div style="height: 400px; background-color: rgba(255, 255, 255, 0.5); padding: 20px; border-radius: 10px;">
            <h3>Key Offerings:</h3>
            <ul>
                <li>Personalized Learning Paths</li>
                <li>Interactive Sensory Games</li>
                <li>Parental Progress Tracking</li>
                <li>Behavioral Support Tools</li>
            </ul>
        </div>
    </section>

    <section id="contact" class="contact-section content-section">
        <h2>Contact Us 📧</h2>
        <p>Get in touch with our support team or learn more about enrollment.</p>
        <div style="height: 350px; background-color: rgba(255, 255, 255, 0.5); padding: 20px; border-radius: 10px; max-width: 600px; margin: 0 auto;">
            <p>Email: svcxzw@gmail.com</p>
            <p>Phone: (123) 456-7890</p>
            <p>Address: 123 Learning Lane, Education City</p>
        </div>
    </section>


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

        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('loginModal');
            const loginButton = document.getElementById('loginButton');
            const closeBtn = document.querySelector('.close-btn');
          
            if (loginButton) {
                loginButton.onclick = function() {
                    modal.style.display = 'flex';
                }
            }
            
            
            if (closeBtn) {
                closeBtn.onclick = function() {
                    modal.style.display = 'none';
                }
            }

           
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }

          
            <?php if ($error): ?>
                modal.style.display = 'flex';
            <?php endif; ?>
        });
    </script>
</body>
</html>
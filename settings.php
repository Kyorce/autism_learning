<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: #BBADD9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #3B2D59;
            margin: 0;
        }

        .settings-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px #7F6DA6;
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #4D3B8C;
        }

        .settings-info {
            text-align: left;
            margin-bottom: 20px;
        }

        .settings-info p {
            font-size: 1.1em;
            margin-bottom: 10px;
            color: #3B2D59;
        }

        .settings-actions {
            margin-top: 20px;
        }

        .settings-actions button,
        .settings-actions a {
            padding: 10px 20px;
            background-color: #4D3B8C;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            margin: 5px;
            text-decoration: none;
            display: inline-block;
        }

        .settings-actions button:hover,
        .settings-actions a:hover {
            background-color: #3B2D59;
        }


        .change-password-form {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #7F6DA6;
            border-radius: 6px;
            text-align: left;
            background-color: #f8f9fa;
        }

        .change-password-form h2 {
            font-size: 1.8em;
            margin-bottom: 15px;
            color: #4D3B8C;
            text-align: left;
        }

        .change-password-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #7F6DA6;
        }

        .change-password-form input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #7F6DA6;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #fff;
            color: #3B2D59;
        }

        .change-password-form button[type="submit"] {
            padding: 10px 15px;
            background-color: #4D3B8C;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .change-password-form button[type="submit"]:hover {
            background-color: #3B2D59;
        }
        .success {
            color: green;
            margin-bottom: 10px;
            border: 1px solid green;
            padding: 10px;
            border-radius: 5px;
            background-color: #e6ffe6;
        }

        .error {
            color: red;
            margin-bottom: 10px;
            border: 1px solid red;
            padding: 10px;
            border-radius: 5px;
            background-color: #ffe6e6;
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <h1>Account Settings</h1>
        <?php if (isset($_SESSION['password_change_message'])): ?>
            <div class="<?= $_SESSION['password_change_message']['type'] ?>">
                <?= $_SESSION['password_change_message']['text'] ?>
            </div>
            <?php unset($_SESSION['password_change_message']); ?>
        <?php endif; ?>
        <div class="settings-info">
            <p><strong>Email:</strong> <?= $_SESSION['email']; ?></p>
        </div>
        <div class="settings-actions">
            <button onclick="togglePasswordForm()">Change Password</button>
            <a href="user.php">Back to Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>

        <div class="change-password-form" id="passwordForm" style="display: none;">
            <h2>Change Password</h2>
            <form action="change_password.php" method="post">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>

                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>

                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <button type="submit" name="change_password">Change Password</button>
            </form>
        </div>
    </div>

    <script>
        function togglePasswordForm() {
            const passwordForm = document.getElementById('passwordForm');
            passwordForm.style.display = passwordForm.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
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
    <title>Your Profile</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: #BBADD9; 
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #3B2D59; 
        }

        .profile-container {
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

        .profile-info {
            text-align: left;
            margin-bottom: 20px;
        }

        .profile-info p {
            font-size: 1.1em;
            margin-bottom: 10px;
            color: #3B2D59; 
        }

        .profile-info strong {
            font-weight: bold;
            color: #7F6DA6; 
        }

        .profile-actions button {
            padding: 10px 20px;
            background-color: #4D3B8C; 
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            margin: 5px;
        }

        .profile-actions button:hover {
            background-color: #3B2D59; 
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h1>Your Profile</h1>
        <div class="profile-info">
            <p><strong>Name:</strong> <?= $_SESSION['name']; ?></p>
            <p><strong>Email:</strong> <?= $_SESSION['email']; ?></p>
        </div>
        <div class="profile-actions">
            <button onclick="window.location.href='user.php'">Back to Dashboard</button>
            <button onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>
</body>
</html>
<?php

require_once 'user_db.php';

$message = '';

if (isset($_POST['add_faculty'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; 
    $department = $_POST['department'];


    $check_sql = "SELECT COUNT(*) FROM faculty WHERE name = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $name);
    $check_stmt->execute();
    $check_stmt->bind_result($name_count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($name_count > 0) {
        $message = "<p style='color:red;'>Faculty member with the name '$name' already exists.</p>";
    } else {
       
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

       
        $sql = "INSERT INTO faculty (name, email, password, department) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $department);

        if ($stmt->execute()) {
            $message = "<p style='color:green;'>Faculty member added successfully!</p>";
        } else {
            $message = "<p style='color:red;'>Error adding faculty member: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Faculty</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #4D3B8C;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #3B2D59;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            margin-top: 5px;
        }
        button[type="submit"] {
            background-color: #4D3B8C;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button[type="submit"]:hover {
            background-color: #3B2D59;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
            border: 1px solid red;
            padding: 10px;
            border-radius: 5px;
            background-color: #ffe6e6;
        }
        .success-message {
            color: green;
            margin-bottom: 10px;
            border: 1px solid green;
            padding: 10px;
            border-radius: 5px;
            background-color: #e6ffe6;
        }
        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            color: #4D3B8C;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Faculty</h2>
        <?php echo $message; ?>
        <form method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="department">Department:</label>
                <input type="text" id="department" name="department" required>
            </div>
            <button type="submit" name="add_faculty">Add Faculty</button>
        </form>
        <a href="faculty_info.php" class="back-link">Back to Faculty Info</a>
    </div>
</body>
</html>
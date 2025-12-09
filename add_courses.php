<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}


require_once 'user_db.php';

$message = '';


if (isset($_POST['add_course'])) {
    
    $name = htmlspecialchars(trim($_POST['name']));
    $code = htmlspecialchars(trim($_POST['code']));
    $description = htmlspecialchars(trim($_POST['description']));

   
    if (empty($name) || empty($code) || empty($description)) {
        $message = '<div class="error-message">All fields are required.</div>';
    } else {
    
        $check_sql = "SELECT course_id FROM courses WHERE course_code = ?";
        $check_stmt = $conn->prepare($check_sql);

        if ($check_stmt) {
            $check_stmt->bind_param("s", $code);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                $message = '<div class="error-message">A course with the code "' . htmlspecialchars($code) . '" already exists. Please use a different code.</div>';
            } else {
              
                $sql = "INSERT INTO courses (course_name, course_code, description) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param("sss", $name, $code, $description);

                    if ($stmt->execute()) {
                        $message = '<div class="success-message">Course added successfully!</div>';
                    } else {
                        $message = '<div class="error-message">Error adding course: ' . $stmt->error . '</div>';
                    }

                    $stmt->close();
                } else {
                    $message = '<div class="error-message">Error preparing SQL statement for insertion: ' . $conn->error . '</div>';
                }
            }

            $check_stmt->close();
        } else {
            $message = '<div class="error-message">Error preparing SQL statement for checking course code: ' . $conn->error . '</div>';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Course</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ... (Your existing styles remain the same) ... */
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
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #3B2D59;
            font-weight: bold;
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            margin-top: 5px;
            font-size: 1em;
        }
        textarea {
            resize: vertical;
        }
        button[type="submit"] {
            background-color: #4D3B8C;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }
        button[type="submit"]:hover {
            background-color: #3B2D59;
        }
        .error-message {
            color: #dc3545;
            margin-bottom: 10px;
            border: 1px solid #f8d7da;
            padding: 10px;
            border-radius: 5px;
            background-color: #fdecea;
            font-size: 0.9em;
        }
        .success-message {
            color: #28a745;
            margin-bottom: 10px;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 5px;
            background-color: #d4edda;
            font-size: 0.9em;
        }
        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            color: #4D3B8C;
            font-size: 0.9em;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Course</h2>
        <?php echo $message; ?>
        <form method="post">
            <div class="form-group">
                <label for="name">Course:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="code">Course Code:</label>
                <input type="text" id="code" name="code" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            <button type="submit" name="add_course">Add Course</button>
        </form>
        <a href="courses.php" class="back-link">Back to Courses</a>
    </div>
</body>
</html>
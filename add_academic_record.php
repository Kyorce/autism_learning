<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

require_once 'user_db.php'; 

$message = '';

if (isset($_POST['add_record'])) {
    $degree = htmlspecialchars(trim($_POST['degree']));
    $major = htmlspecialchars(trim($_POST['major']));
    $institution = htmlspecialchars(trim($_POST['institution']));
    $graduation_year = htmlspecialchars(trim($_POST['graduation_year']));
    $honors_received = htmlspecialchars(trim($_POST['honors_received']));
    $user_email = $_SESSION['email'];


    if (empty($degree) || empty($institution) || empty($graduation_year)) {
        $message = '<div class="error-message">Degree, Institution, and Graduation Year are required.</div>';
    } else if (!is_numeric($graduation_year)) {
        $message = '<div class="error-message">Graduation Year must be a number.</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO academic_records (user_email, degree, major, institution, graduation_year, honors_received) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssis", $user_email, $degree, $major, $institution, $graduation_year, $honors_received);

        if ($stmt->execute()) {
            $message = '<div class="success-message">Academic record added successfully! <a href="academic_record.php">View your records</a>.</div>';
          
            $_POST = array();
        } else {
            $message = '<div class="error-message">Error adding record: ' . $stmt->error . '</div>';
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
    <title>Add Academic Record</title>
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

        .add-record-form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px #7F6DA6;
            width: 100%;
            max-width: 600px;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #4D3B8C;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #3B2D59;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1em;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1em;
            resize: vertical;
            min-height: 80px;
        }

        button[type="submit"], .back-button {
            padding: 10px 20px;
            background-color: #4D3B8C;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        button[type="submit"]:hover, .back-button:hover {
            background-color: #3B2D59;
        }

        .error-message {
            color: #dc3545;
            margin-bottom: 10px;
            border: 1px solid #f8d7da;
            padding: 10px;
            border-radius: 5px;
            background-color: #fdecea;
            text-align: center;
        }

        .success-message {
            color: #28a745;
            margin-bottom: 10px;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 5px;
            background-color: #d4edda;
            text-align: center;
        }

        .back-button {
            display: block;
            text-align: center;
            text-decoration: none;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="add-record-form-container">
        <h1>Add Academic Record</h1>
        <?php echo $message; ?>
        <form method="post">
            <div class="form-group">
                <label for="degree">Degree:</label>
                <input type="text" id="degree" name="degree" required>
            </div>
            <div class="form-group">
                <label for="major">Major (Optional):</label>
                <input type="text" id="major" name="major">
            </div>
            <div class="form-group">
                <label for="institution">Institution:</label>
                <input type="text" id="institution" name="institution" required>
            </div>
            <div class="form-group">
                <label for="graduation_year">Graduation Year:</label>
                <input type="number" id="graduation_year" name="graduation_year" required>
            </div>
            <div class="form-group">
                <label for="honors_received">Honors Received (Optional):</label>
                <textarea id="honors_received" name="honors_received"></textarea>
            </div>
            <button type="submit" name="add_record">Add Record</button>
        </form>
        <a href="academic_record.php" class="back-button">Back to Academic Record</a>
    </div>
</body>
</html>
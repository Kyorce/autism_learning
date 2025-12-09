<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

require_once 'user_db.php';


$user_email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT id, degree, major, institution, graduation_year, honors_received FROM academic_records WHERE user_email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$academicRecords = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Record</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: #BBADD9;
            display: flex;
            flex-direction: column; 
            justify-content: flex-start; 
            align-items: center;
            min-height: 100vh;
            color: #3B2D59;
            padding-top: 60px;
            box-sizing: border-box;
        }

        .academic-record-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px #7F6DA6;
            width: 100%;
            max-width: 800px;
            margin-bottom: 20px; 
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #4D3B8C;
            text-align: center;
        }

        .add-record-button-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .add-record-button {
            padding: 10px 20px;
            background-color: #28a745; 
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }

        .add-record-button:hover {
            background-color: #1e7e34;
        }

        .record-section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: #f8f9fa;
        }

        .record-section h2 {
            font-size: 1.5em;
            color: #4D3B8C;
            margin-top: 0;
            margin-bottom: 10px;
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
        }

        .record-item {
            display: flex;
            margin-bottom: 8px;
        }

        .record-item strong {
            width: 150px;
            font-weight: bold;
            color: #7F6DA6;
        }

        .record-item span {
            flex-grow: 1;
            color: #3B2D59;
        }

        .back-button {
            display: block;
            padding: 10px 20px;
            background-color: #4D3B8C;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            text-align: center;
            text-decoration: none;
            margin-top: 20px;
        }

        .back-button:hover {
            background-color: #3B2D59;
        }

        .no-records {
            text-align: center;
            color: #777;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="academic-record-container">
        <h1>Academic Record</h1>

        <div class="add-record-button-container">
            <a href="add_academic_record.php" class="add-record-button">Add New Record</a>
        </div>

        <?php if (!empty($academicRecords)): ?>
            <?php foreach ($academicRecords as $record): ?>
                <div class="record-section">
                    <h2>Education</h2>
                    <div class="record-item">
                        <strong>Degree:</strong> <span><?= htmlspecialchars($record['degree']); ?></span>
                    </div>
                    <div class="record-item">
                        <strong>Major:</strong> <span><?= htmlspecialchars($record['major']); ?></span>
                    </div>
                    <div class="record-item">
                        <strong>Institution:</strong> <span><?= htmlspecialchars($record['institution']); ?></span>
                    </div>
                    <div class="record-item">
                        <strong>Graduation Year:</strong> <span><?= htmlspecialchars($record['graduation_year']); ?></span>
                    </div>
                    <?php if (!empty($record['honors_received'])): ?>
                        <div class="record-item">
                            <strong>Honors Received:</strong> <span><?= htmlspecialchars($record['honors_received']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-records">No academic records found for your account. Click "Add New Record" to add one.</p>
        <?php endif; ?>

        <a href="user.php" class="back-button">Back to Dashboard</a>
    </div>
</body>
</html>
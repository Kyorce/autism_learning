<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once 'user_db.php';

function getAllCourses(mysqli $conn): array
{
    $sql = "SELECT course_id, course_name, course_code, description FROM courses";
    $result = $conn->query($sql);
    $courses = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
        $result->free();
    }
    return $courses;
}

$coursesData = getAllCourses($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Courses</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: sans-serif;
            background-color: #BBADD9;
            margin: 0;
            color: #3B2D59;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 800px;
            margin: 20px auto 0 auto;
        }

        h2 {
            color: #4D3B8C;
            margin-bottom: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #7F6DA6;
        }

        th, td {
            border: 1px solid #7F6DA6;
            padding: 10px;
            text-align: left;
            color: #3B2D59;
        }

        th {
            background-color: #4D3B8C;
            color: #BBADD9;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f0e6ff;
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
        <h2>View Courses</h2>

        <?php if (empty($coursesData)): ?>
            <p style="color: #3B2D59;">No courses found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th style="background-color: #4D3B8C; color: #BBADD9;">ID</th>
                        <th style="background-color: #4D3B8C; color: #BBADD9;">Name</th>
                        <th style="background-color: #4D3B8C; color: #BBADD9;">Code</th>
                        <th style="background-color: #4D3B8C; color: #BBADD9;">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($coursesData as $course): ?>
                        <tr>
                            <td style="border: 1px solid #7F6DA6; color: #3B2D59;"><?= htmlspecialchars($course['course_id']) ?></td>
                            <td style="border: 1px solid #7F6DA6; color: #3B2D59;"><?= htmlspecialchars($course['course_name']) ?></td>
                            <td style="border: 1px solid #7F6DA6; color: #3B2D59;"><?= htmlspecialchars($course['course_code']) ?></td>
                            <td style="border: 1px solid #7F6DA6; color: #3B2D59;"><?= htmlspecialchars($course['description']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div style="margin-top: 20px; text-align: center;">
            <a href="admin.php" class="back-link">Back to Admin Dashboard</a>
        </div>
    </div>
</body>
</html>
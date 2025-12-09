<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}


if (!isset($_SESSION['user_id'])) {
    die("User ID not found in session."); 
}
$user_id = $_SESSION['user_id'];

require_once 'user_db.php';

$sql = "SELECT t.*, c.course_name, p.name as professor_name
        FROM timetables t
        JOIN courses c ON t.course_id = c.course_id
        JOIN professors p ON t.faculty_id = p.professor_id
        WHERE t.faculty_id = ?
        ORDER BY t.day_of_week, t.start_time";

$stmt = $conn->prepare($sql);
$timetable_slots = [];

if ($stmt) {
    $stmt->bind_param("i", $user_id); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $timetable_slots[] = $row;
        }
    }
    $stmt->close();
} else {
    die("Error preparing SQL statement: " . $conn->error); 
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Timetable</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", serif;
        }

        body {
            background: #BBADD9;
            color: #3B2D59;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 900px;
        }

        h2 {
            font-size: 28px;
            color: #4D3B8C;
            margin-bottom: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            font-size: 16px;
        }

        th {
            background-color: #4D3B8C;
            color: white;
            font-weight: 500;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f0f0f0;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .back-link a:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Timetable</h2>
        <table>
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Professor</th>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Room</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($timetable_slots)): ?>
                    <tr><td colspan="6">No timetable slots assigned to you.</td></tr>
                <?php else: ?>
                    <?php foreach ($timetable_slots as $slot): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($slot['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($slot['professor_name']); ?></td>
                            <td><?php echo htmlspecialchars($slot['day_of_week']); ?></td>
                            <td><?php echo htmlspecialchars($slot['start_time']); ?></td>
                            <td><?php echo htmlspecialchars($slot['end_time']); ?></td>
                            <td><?php echo htmlspecialchars($slot['room']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="back-link">
            <a href="user.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
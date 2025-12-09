<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
require_once 'user_db.php';

$message = '';

if (isset($_POST['add_timetable'])) {
    $course_id = $_POST['course_id'];
    $user_id = $_POST['user_id']; 
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $room = $_POST['room'];

  
    if (empty($course_id) || empty($user_id) || empty($day_of_week) || empty($start_time) || empty($end_time)) {
        $message = '<div class="error-message">All fields are required.</div>';
    } else {
        $sql = "INSERT INTO timetables (course_id, faculty_id, day_of_week, start_time, end_time, room)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql); 

        if ($stmt) { 
            $stmt->bind_param("iissss", $course_id, $user_id, $day_of_week, $start_time, $end_time, $room);

            if ($stmt->execute()) {
                $message = '<div class="success-message">Timetable slot added successfully!</div>';
            } else {
                $message = '<div class="error-message">Error adding slot: ' . $stmt->error . '</div>';
            }
            $stmt->close();
        } else {
            $message = '<div class="error-message">Error preparing SQL statement: ' . $conn->error . '</div>';
        }
    }
}


$courses = getAllCourses($conn);

$users = getAllUsersByRole($conn, 'user'); 

function getAllCourses(mysqli $conn): array {
    $sql = "SELECT course_id, course_name FROM courses";
    $result = $conn->query($sql);
    $courses = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
    }
    return $courses;
}

function getAllUsersByRole(mysqli $conn, string $role): array {
    $sql = "SELECT id, name FROM user WHERE role = ?";
    $stmt = $conn->prepare($sql);
    $users = [];
    if ($stmt) {
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        $stmt->close();
    }
    return $users;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Timetable Slot</title>
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
        select, input[type="time"], input[type="text"] {
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
        <h2>Add Timetable Slot</h2>
        <?php echo $message; ?>
        <form method="post">
            <div class="form-group">
                <label for="course_id">Course:</label>
                <select id="course_id" name="course_id" required>
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['course_id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="user_id">User:</label>
                <select id="user_id" name="user_id" required>
                    <option value="">Select User</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="day_of_week">Day of Week:</label>
                <select id="day_of_week" name="day_of_week" required>
                    <option value="">Select Day</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
            </div>
            <div class="form-group">
                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time" required>
            </div>
            <div class="form-group">
                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time" required>
            </div>
            <div class="form-group">
                <label for="room">Room:</label>
                <input type="text" id="room" name="room" required>
            </div>
            <button type="submit" name="add_timetable">Add Slot</button>
        </form>
        <a href="view_timetable.php" class="back-link">Back to Timetable</a>
    </div>
</body>
</html>
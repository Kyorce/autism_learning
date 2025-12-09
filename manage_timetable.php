<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once 'user_db.php';


$message = '';
$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
$timeslots = ["8:00-9:00", "9:00-10:00", "10:00-11:00", "11:00-12:00", "12:00-13:00", "13:00-14:00", "14:00-15:00", "15:00-16:00"];  


$courses = [];
$facultyMembers = [];

$courses_sql = "SELECT course_id, course_name FROM courses";
$courses_result = $conn->query($courses_sql);
if ($courses_result && $courses_result->num_rows > 0) {
    while ($row = $courses_result->fetch_assoc()) {
        $courses[] = $row;
    }
}

$faculty_sql = "SELECT professor_id, name FROM professors";
$faculty_result = $conn->query($faculty_sql);
if ($faculty_result && $faculty_result->num_rows > 0) {
    while ($row = $faculty_result->fetch_assoc()) {
        $facultyMembers[] = $row;
    }
}


if (isset($_POST['add_timetable_entry'])) {
    $course_id = $_POST['course_id'];
    $professor_id = $_POST['professor_id'];
    $day = $_POST['day'];
    $timeslot = $_POST['timeslot'];

    if (empty($course_id) || empty($professor_id) || empty($day) || empty($timeslot)) {
        $message = '<div class="error-message">All fields are required.</div>';
    } else {
        $sql = "INSERT INTO timetable (course_id, professor_id, day, timeslot) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $course_id, $professor_id, $day, $timeslot);

        if ($stmt->execute()) {
            $message = '<div class="success-message">Timetable entry added successfully!</div>';
        } else {
            $message = '<div class="error-message">Error adding timetable entry: ' . $stmt->error . '</div>';
        }

        $stmt->close();
    }
}



function displayTimetable($conn, $days, $timeslots) {
    $sql = "SELECT 
                t.timetable_id, 
                c.course_name, 
                p.name as professor_name, 
                t.day, 
                t.timeslot
            FROM 
                timetable t
            JOIN 
                courses c ON t.course_id = c.course_id
            JOIN 
                professors p ON t.professor_id = p.professor_id
            ORDER BY 
                FIELD(t.day, '" . implode("','", $days) . "'), 
                FIELD(t.timeslot, '" . implode("','", $timeslots) . "')";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        echo "<table>";
        echo "<thead><tr><th>Course</th><th>Professor</th><th>Day</th><th>Timeslot</th></tr></thead>";
        echo "<tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['professor_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['day']) . "</td>";
            echo "<td>" . htmlspecialchars($row['timeslot']) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No timetable entries found.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Timetable</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Add timetable-specific styles here */
        .timetable-container {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4D3B8C;
            color: white;
        }
        /* Error and success messages */
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Timetable</h2>
        <?php echo $message; ?>

        <div class="add-timetable-form">
            <h3>Add New Timetable Entry</h3>
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
                    <label for="professor_id">Faculty:</label>
                    <select id="professor_id" name="professor_id" required>
                        <option value="">Select Faculty</option>
                        <?php foreach ($facultyMembers as $faculty): ?>
                            <option value="<?php echo $faculty['professor_id']; ?>"><?php echo htmlspecialchars($faculty['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="day">Day:</label>
                    <select id="day" name="day" required>
                        <option value="">Select Day</option>
                        <?php foreach ($days as $day): ?>
                            <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="timeslot">Timeslot:</label>
                    <select id="timeslot" name="timeslot" required>
                        <option value="">Select Timeslot</option>
                        <?php foreach ($timeslots as $timeslot): ?>
                            <option value="<?php echo $timeslot; ?>"><?php echo $timeslot; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="add_timetable_entry">Add Entry</button>
            </form>
        </div>

        <div class="timetable-container">
            <h3>Timetable</h3>
            <?php displayTimetable($conn, $days, $timeslots); ?>
        </div>
        <a href="admin.php">Back to Admin Dashboard</a>
    </div>
</body>
</html>
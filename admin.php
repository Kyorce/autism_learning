<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require 'user_db.php';

$message = '';
$existing_announcements = [];
$timetable_message = ''; // Message for timetable operations

$sql_select_announcements = "SELECT id, title, content, created_at FROM announcements ORDER BY created_at DESC";
$result_announcements = $conn->query($sql_select_announcements);

if ($result_announcements) {
    $existing_announcements = $result_announcements->fetch_all(MYSQLI_ASSOC);
    $result_announcements->free_result();
} else {
    error_log("Error fetching existing announcements for admin: " . $conn->error);
    $message = "<p style='color: red;'>Error retrieving existing announcements.</p>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_announcement'])) {
    $title = $_POST['announcement_title'];
    $content = $_POST['announcement_content'];

    if (!empty($title) && !empty($content)) {

        $sql_announcement = "INSERT INTO announcements (title, content, created_at) VALUES (?, ?, NOW())";
        $stmt_announcement = $conn->prepare($sql_announcement);

        if ($stmt_announcement) {
            $stmt_announcement->bind_param("ss", $title, $content);
            if ($stmt_announcement->execute()) {
                $announcement_id = $conn->insert_id;


                $sql_users = "SELECT id FROM user WHERE role = 'user'";
                $result_users = $conn->query($sql_users);

                if ($result_users) {
                    $notification_message = "New announcement posted: " . htmlspecialchars($title);
                    $sql_notification = "INSERT INTO notifications (user_id, message, timestamp) VALUES (?, ?, NOW())";
                    $stmt_notification = $conn->prepare($sql_notification);

                    if ($stmt_notification) {
                        while ($row_user = $result_users->fetch_assoc()) {
                            $user_id = $row_user['id'];
                            $stmt_notification->bind_param("is", $user_id, $notification_message);
                            $stmt_notification->execute();
                        }
                        $stmt_notification->close();
                        $message = "<p style='color: green;'>Announcement created and users notified successfully!</p>";


                        $sql_select_announcements_refresh = "SELECT id, title, content, created_at FROM announcements ORDER BY created_at DESC";
                        $result_announcements_refresh = $conn->query($sql_select_announcements_refresh);
                        if ($result_announcements_refresh) {
                            $existing_announcements = $result_announcements_refresh->fetch_all(MYSQLI_ASSOC);
                            $result_announcements_refresh->free_result();
                        } else {
                            error_log("Error refreshing announcements for admin: " . $conn->error);
                        }

                    } else {
                        $message = "<p style='color: red;'>Error preparing notification SQL statement: " . $conn->error . "</p>";
                    }
                    $result_users->free_result();
                } else {
                    $message = "<p style='color: orange;'>Announcement created, but error fetching users for notifications: " . $conn->error . "</p>";
                }
            } else {
                $message = "<p style='color: red;'>Error creating announcement: " . $stmt_announcement->error . "</p>";
            }
            $stmt_announcement->close();
        } else {
            $message = "<p style='color: red;'>Error preparing announcement SQL statement: " . $conn->error . "</p>";
        }
    } else {
        $message = "<p style='color: orange;'>Please fill in both the title and content.</p>";
    }
}

// Handle Timetable Submission (Assuming a form with 'submit_timetable' name)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_timetable'])) {
    $course_id = $_POST['course_id'];
    $faculty_id = $_POST['faculty_id']; // Assuming you have this
    $day_of_week = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $room = $_POST['room'];
    $user_id = $_POST['user_id']; // Get the selected user ID

    $sql_insert_timetable = "INSERT INTO timetables (course_id, faculty_id, day_of_week, start_time, end_time, room, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert_timetable = $conn->prepare($sql_insert_timetable);

    if ($stmt_insert_timetable) {
        $stmt_insert_timetable->bind_param("iisssssi", $course_id, $faculty_id, $day_of_week, $start_time, $end_time, $room, $user_id);

        if ($stmt_insert_timetable->execute()) {
            $timetable_message = "<p style='color: green;'>Timetable slot added successfully!</p>";
        } else {
            $timetable_message = "<p style='color: red;'>Error adding timetable slot: " . $stmt_insert_timetable->error . "</p>";
            error_log("Error inserting timetable: " . $stmt_insert_timetable->error);
        }
        $stmt_insert_timetable->close();
    } else {
        $timetable_message = "<p style='color: red;'>Error preparing SQL statement: " . $conn->error . "</p>";
        error_log("Error preparing insert timetable SQL: " . $conn->error);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: #BBADD9;
            color: #3B2D59;
            margin: 0;
            overflow-x: hidden;
            display: flex;
        }

        .top-menu {
            background-color: #4D3B8C;
            color: #BBADD9;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 20;
            justify-content: flex-end;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .top-menu h2 {
            display: none;
        }

        .top-right-info {
            display: flex;
            align-items: center;
        }

        .top-right-info span {
            font-size: 1em;
            margin-left: 10px;
        }

        .sidebar {
            background-color: #3B2D59;
            width: 60px;
            z-index: 21;
            color: #BBADD9;
            overflow-y: auto;
            padding-top: 56px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: width 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar.open {
            width: 200px;
            align-items: flex-start;
        }

        .menu-button {
            background: none;
            color: #BBADD9;
            border: none;
            padding: 15px;
            cursor: pointer;
            font-size: 1.5em;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            flex-direction: column;
            margin-bottom: 20px;
        }

        .menu-icon {
            display: block;
        }

        .menu-text {
            opacity: 0;
            margin-top: 5px;
            transition: opacity 0.3s ease;
            font-size: 0.8em;
        }

        .sidebar.open .menu-button {
            flex-direction: row;
            justify-content: flex-start;
        }

        .sidebar.open .menu-icon {
            margin-right: 10px;
        }

        .sidebar.open .menu-text {
            opacity: 1;
        }

        .sidebar-link {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #BBADD9;
            transition: background-color 0.2s ease;
            text-align: left;
            width: 100%;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 0.9em;
            opacity: 0;
            transform: translateX(-20px);
            transition: opacity 0.3s ease, transform 0.3s ease;
            margin-bottom: 5px;
        }

        .sidebar.open .sidebar-link {
            opacity: 1;
            transform: translateX(0);
        }

        .sidebar-link:hover {
            background-color: #7F6DA6;
        }

        .submenu {
            margin-left: 20px;
            border-left: 1px solid #7F6DA6;
        }

        .submenu-link {
            display: block;
            padding: 8px 15px;
            text-decoration: none;
            color: #D9B3FF;
            transition: background-color 0.2s ease;
            text-align: left;
            width: 100%;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 0.85em;
            opacity: 0;
            transform: translateX(-20px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .sidebar.open .submenu-link {
            opacity: 1;
            transform: translateX(0);
        }

        .submenu-link:hover {
            background-color: #9A86D1;
        }

        .sidebar-bottom {
            margin-top: auto;
            width: 100%;
        }

        .sidebar-bottom .sidebar-link,
        .sidebar-bottom .sidebar-logout-button {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #BBADD9;
            transition: background-color 0.2s ease;
            text-align: left;
            width: 100%;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 0.9em;
            opacity: 0;
            transform: translateX(-20px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .sidebar.open .sidebar-bottom .sidebar-link,
        .sidebar.open .sidebar-bottom .sidebar-logout-button {
            opacity: 1;
            transform: translateX(0);
        }

        .sidebar-bottom .sidebar-link:hover,
        .sidebar-bottom .sidebar-logout-button:hover {
            background-color: #7F6DA6;
        }

        .sidebar-logout-button {
            margin-bottom: 10px;
        }

        .dashboard-content {
            padding: 20px;
            margin: 15px;
            margin-top: 70px;
            margin-left: 75px;
            transition: margin-left 0.3s ease;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
        }

        .dashboard-content.sidebar-open {
            margin-left: 215px;
        }

        h1 {
            font-size: 2em;
            margin-bottom: 15px;
            color: #4D3B8C;
        }

        span {
            color: #7F6DA6;
        }

        p {
            font-size: 0.95em;
            margin-bottom: 10px;
            color: #3B2D59;
        }

        .admin-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        .admin-info p {
            margin-bottom: 8px;
            color: #3B2D59;
        }

        .announcement-form {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        .announcement-form h2 {
            color: #4D3B8C;
            margin-bottom: 10px;
            font-size: 1.5em;
        }

        .announcement-form label {
            display: block;
            margin-bottom: 5px;
            color: #3B2D59;
            font-weight: bold;
        }

        .announcement-form input[type="text"],
        .announcement-form textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 0.9em;
        }

        .announcement-form textarea {
            resize: vertical;
            min-height: 100px;
        }

        .announcement-form button[type="submit"] {
            background-color: #5cb85c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
        }

        .announcement-form button[type="submit"]:hover {
            background-color: #4cae4c;
        }

        .existing-announcements {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        .existing-announcements h2 {
            color: #4D3B8C;
            margin-bottom: 10px;
            font-size: 1.5em;
        }

        .announcement-preview {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background-color: #fff;
        }

        .announcement-preview h3 {
            color: #3B2D59;
            margin-top: 0;
            margin-bottom: 5px;
            font-size: 1.1em;
        }

        .announcement-preview p {
            color: #555;
            font-size: 0.9em;
            margin-bottom: 8px;
        }

        .announcement-preview .date {
            color: #777;
            font-size: 0.8em;
            text-align: right;
        }

    
        .announcement-preview {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background-color: #fff;
        }

        .announcement-preview h3 {
            color: #3B2D59;
            margin-top: 0;
            margin-bottom: 5px;
            font-size: 1.1em;
        }

        .announcement-preview p {
            color: #555;
            font-size: 0.9em;
            margin-bottom: 8px;
        }

        .announcement-preview .date {
            color: #777;
            font-size: 0.8em;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="top-menu">
        <div class="top-right-info">
            <span style="color: #D98FCC;">Admin</span>
            <span class="dashboard-text">Dashboard</span>
        </div>
    </div>
<div class="sidebar" id="sidebar">
        <button class="menu-button" onclick="toggleSidebar()">
            <div class="menu-icon">☰</div>
            <div class="menu-text">Menu</div>
        </button>

    <div class="sidebar-link" onclick="toggleTimetableSubmenu()">Timetable</div>
        <div id="timetableSubmenu" class="submenu" style="display: none;">
            <a href="view_timetable.php" class="submenu-link">View Timetable</a>
            <a href="add_timetable.php" class="submenu-link">Add Timetable</a>
        </div>

        <div class="sidebar-link" onclick="toggleCoursesSubmenu()">Courses</div>
        <div id="coursesSubmenu" class="submenu" style="display: none;">
            <a href="courses.php" class="submenu-link">View Courses</a>
            <a href="add_courses.php" class="submenu-link">Add New Course</a>
            <a href="add_courses.php" class="submenu-link">Assigned</a>
        </div>

        <a href="departments.php" class="sidebar-link">Departments</a>
        <a href="departments.php" class="sidebar-link">Evaluation</a>
        <a href="departments.php" class="sidebar-link">Students Grade</a>
        <a href="departments.php" class="sidebar-link">Attendance and Leave</a>
    <div class="sidebar-bottom">
            <a href="manage_users.php" class="sidebar-link">Manage Users</a>
            <a href="faculty_info.php" class="sidebar-link">Monitoring Account</a>
            <button onclick="window.location.href='logout.php'" class="sidebar-logout-button">Logout</button>
        </div>
    </div>

    <div class="dashboard-content" id="dashboardContent">
        <h1>Admin Dashboard</h1>

        <div class="admin-info">
            <p>Logged in as: <strong><?= htmlspecialchars($_SESSION['name']); ?></strong> (Admin)</p>
            <p>Your Email: <?= htmlspecialchars($_SESSION['email']); ?></p>
        </div>

        <div class="announcement-form">
            <h2>Create Announcement</h2>
            <form method="post" action="">
                <label for="announcement_title">Title:</label>
                <input type="text" name="announcement_title" id="announcement_title" required>
                <label for="announcement_content">Content:</label>
                <textarea name="announcement_content" id="announcement_content" required></textarea>
                <button type="submit" name="submit_announcement">Post Announcement</button>
                <?php echo $message; ?>
            </form>
        </div>

        <div class="existing-announcements">
            <h2>Existing Announcements</h2>
            <?php if (empty($existing_announcements)): ?>
                <p>No announcements have been posted yet.</p>
            <?php else: ?>
                <?php foreach ($existing_announcements as $announcement): ?>
                    <div class="announcement-preview">
                        <h3><?= htmlspecialchars($announcement['title']); ?></h3>
                        <p><?= htmlspecialchars($announcement['content']); ?></p>
                        <p class="date">Posted: <?= date('F j, Y, g:i a', strtotime($announcement['created_at'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const dashboardContent = document.getElementById('dashboardContent');
        const timetableSubmenu = document.getElementById('timetableSubmenu');
        let isTimetableSubmenuOpen = false;
        const coursesSubmenu = document.getElementById('coursesSubmenu');
        let isCoursesSubmenuOpen = false;

        function toggleSidebar() {
            sidebar.classList.toggle('open');
            dashboardContent.classList.toggle('sidebar-open');

            if (!sidebar.classList.contains('open')) {
                if (isTimetableSubmenuOpen) {
                    timetableSubmenu.style.display = 'none';
                    isTimetableSubmenuOpen = false;
                }
                if (isCoursesSubmenuOpen) {
                    coursesSubmenu.style.display = 'none';
                    isCoursesSubmenuOpen = false;
                }
            }
        }

        function toggleTimetableSubmenu() {
            timetableSubmenu.style.display = isTimetableSubmenuOpen ? 'none' : 'block';
            isTimetableSubmenuOpen = !isTimetableSubmenuOpen;
            openSidebarIfNeeded();
        }

        function toggleCoursesSubmenu() {
            coursesSubmenu.style.display = isCoursesSubmenuOpen ? 'none' : 'block';
            isCoursesSubmenuOpen = !isCoursesSubmenuOpen;
            openSidebarIfNeeded();
        }

        function openSidebarIfNeeded() {
            if (!sidebar.classList.contains('open')) {
                toggleSidebar();
            }
        }


        if (window.innerWidth >= 768) {
            sidebar.classList.remove('open');
            dashboardContent.classList.remove('sidebar-open');
        } else {
            timetableSubmenu.style.display = 'none';
            coursesSubmenu.style.display = 'none';
        }
    </script>

</body>
</html>

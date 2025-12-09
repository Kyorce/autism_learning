<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

require 'user_db.php'; 

$announcements = []; 

if ($conn) {
    $userId = $_SESSION['user_id'];

    $sql_announcements = "SELECT title, content, created_at FROM announcements ORDER BY created_at DESC LIMIT 5"; 
    $result_announcements = $conn->query($sql_announcements);

    if ($result_announcements) {
        $announcements = $result_announcements->fetch_all(MYSQLI_ASSOC);
        $result_announcements->free_result();
    } else {
        error_log("Error fetching announcements: " . $conn->error);
    }

    $conn->close();
} else {
    error_log("Database connection not established.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
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

        .user-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        .user-info p {
            margin-bottom: 8px;
            color: #3B2D59;
        }

        .announcement-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        .announcement-section h2 {
            color: #4D3B8C;
            margin-bottom: 10px;
            font-size: 1.5em;
        }

        .announcement-item {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 4px;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
        }

        .announcement-item h3 {
            color: #3B2D59;
            margin-top: 0;
            margin-bottom: 5px;
            font-size: 1.1em;
        }

        .announcement-item p {
            color: #3B2D59;
            font-size: 0.9em;
            margin-bottom: 8px;
        }

        .announcement-item .date {
            color: #777;
            font-size: 0.8em;
        }
    </style>
</head>

<body>
    <div class="top-menu">
        <div class="top-right-info">
            <span style="color: #D98FCC;">User</span>
            <span class="dashboard-text">Dashboard</span>
        </div>
    </div>

    <div class="sidebar" id="sidebar">
        <button class="menu-button" onclick="toggleSidebar()">
            <div class="menu-icon">☰</div>
            <div class="menu-text">Menu</div>
        </button>

        <div class="sidebar-link" onclick="toggleProfileSubmenu()">Profile</div>
        <div id="profileSubmenu" class="submenu" style="display: none;">
            <a href="profile.php" class="submenu-link">View Profile</a>
            <a href="academic_record.php" class="submenu-link">Academic Record</a>
            <a href="#" class="submenu-link">Non-Teaching Workload</a>
        </div>

        <a href="view_user_timetable.php" class="sidebar-link">Timetable</a>
        <a href="#" class="sidebar-link">Attendance and Leave</a>
        <a href="#" class="sidebar-link">Student Grades</a>
        <div class="sidebar-bottom">
            <a href="announcements.php" class="sidebar-link">Announcement</a>
            <a href="notifications.php" class="sidebar-link">Notification</a>
            <a href="settings.php" class="sidebar-link">Account Settings</a>

            <button onclick="window.location.href='logout.php'" class="sidebar-logout-button">Logout</button>
        </div>
    </div>

    <div class="dashboard-content" id="dashboardContent">
        <h1>Welcome, <span style="color: #D98FCC;"><?= htmlspecialchars($_SESSION['name']); ?></span>!</h1>
        <p>User Dashboard.</p>

        <div class="user-info">
            <p>Your Email: <?= htmlspecialchars($_SESSION['email']); ?></p>
        </div>

        <?php if (!empty($announcements)): ?>
            <div class="announcement-section">
                <h2>Latest Announcements</h2>
                <?php foreach ($announcements as $announcement): ?>
                    <div class="announcement-item">
                        <h3><?= htmlspecialchars($announcement['title']); ?></h3>
                        <p><?= nl2br(htmlspecialchars($announcement['content'])); ?></p>
                        <p class="date">Posted on: <?= date('F j, Y, g:i a', strtotime($announcement['created_at'])); ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if (count($announcements) >= 5): ?>
                    <p><a href="announcements.php">View All Announcements</a></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="announcement-section">
                <h2>Latest Announcements</h2>
                <p>No announcements available at the moment.</p>
            </div>
        <?php endif; ?>
        </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const dashboardContent = document.getElementById('dashboardContent');
        const profileSubmenu = document.getElementById('profileSubmenu');
        let isProfileSubmenuOpen = false;

        function toggleSidebar() {
            sidebar.classList.toggle('open');
            dashboardContent.classList.toggle('sidebar-open');

            if (!sidebar.classList.contains('open')) {
                if (isProfileSubmenuOpen) {
                    profileSubmenu.style.display = 'none';
                    isProfileSubmenuOpen = false;
                }
            }
        }

        function toggleProfileSubmenu() {
            profileSubmenu.style.display = isProfileSubmenuOpen ? 'none' : 'block';
            isProfileSubmenuOpen = !isProfileSubmenuOpen;
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
            profileSubmenu.style.display = 'none';
        }
    </script>

</body>

</html>
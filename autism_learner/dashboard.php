<?php
include 'db_connect.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();


$theme_class = $user['theme_color'] ?? 'default';


$menu_items = [
    'dashboard.php' => ['icon' => '🏠', 'name' => 'Dashboard'],
    'games.php' => ['icon' => '🎮', 'name' => 'Autism Games'],
    'learning.php' => ['icon' => '📚', 'name' => 'Learning/Reading'],
    'stories.php' => ['icon' => '📖', 'name' => 'Story Books'],
    'abc.php' => ['icon' => '🔠', 'name' => 'ABC'],
    'songs.php' => ['icon' => '🎵', 'name' => 'Kids Song'],
    'settings.php' => ['icon' => '⚙️', 'name' => 'Settings'],
    'logout.php' => ['icon' => '🚪', 'name' => 'Logout']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Learner Dashboard</title>
  
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="style.css">
</head>
<body class="theme-<?php echo $theme_class; ?>">

<div class="wrapper">
    
    
    <button class="sidebar-toggle" id="sidebarToggle">☰</button>

    <div class="sidebar" id="sidebar">
    
      
        <?php foreach ($menu_items as $url => $item): ?>
            <a href="<?php echo $url; ?>"><?php echo $item['icon'] . ' ' . $item['name']; ?></a>
        <?php endforeach; ?>
    </div>

    <div class="content">
        <h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h1>
        <p>This is your personalized dashboard. Use the links on the left to explore your learning activities and games.</p>
        
        <h3>Quick Stats</h3>
        <p>Games Played: 5 | Books Read: 2 | Theme: <?php echo ucfirst($theme_class); ?></p>

    
        <div style="height: 100vh; background: #f0f0f0; padding: 20px; margin-top: 30px; border-radius: 8px;">
            <p>More content space to test the fixed sidebar scroll on desktop.</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('sidebarToggle');
        const closeButton = document.getElementById('closeSidebarBtn');
        const content = document.querySelector('.content');

        const isMobile = window.matchMedia("(max-width: 768px)");

        function setSidebarVisibility() {
            if (isMobile.matches) {
              
                if (sidebar.classList.contains('active')) {
                     closeButton.style.display = 'flex';
                     toggleButton.textContent = '✕'; 
                } else {
                     closeButton.style.display = 'none';
                     toggleButton.textContent = '☰';
                }
            } else {
           
                sidebar.classList.remove('active');
                closeButton.style.display = 'none';
                toggleButton.textContent = '☰';
            }
        }
        
        function toggleSidebar() {
            sidebar.classList.toggle('active');
            setSidebarVisibility();
        }


        toggleButton.addEventListener('click', toggleSidebar);
        closeButton.addEventListener('click', toggleSidebar);
        
        
        isMobile.addEventListener('change', setSidebarVisibility);

        setSidebarVisibility();
    });
</script>

</body>
</html>
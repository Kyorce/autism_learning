<?php
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['theme_color'])) {
    $new_theme = $_POST['theme_color'];
    

    $allowed_themes = ['default', 'blue', 'green', 'pastel', 'monochrome']; 
    if (in_array($new_theme, $allowed_themes)) { 
        
   
        $stmt = $conn->prepare("UPDATE users SET theme_color = ? WHERE id = ?");
        $stmt->bind_param("si", $new_theme, $user_id);
        
        if ($stmt->execute()) {
            $message = "Theme updated successfully! Refresh the page to see changes on other pages.";
        } else {
            $message = "Error updating theme: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Invalid theme selection.";
    }
}

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
    <title>Learner Settings</title>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="style.css">
</head>
<body class="theme-<?php echo $theme_class; ?>">

<div class="wrapper">
    
  
    <button class="sidebar-toggle" id="sidebarToggle">☰</button>

    <div class="sidebar" id="sidebar">
         
        <a href="javascript:void(0)" class="close-btn" style="display: none; text-align: right; justify-content: flex-end;" id="closeSidebarBtn">
            &times; Close
        </a>
        <?php foreach ($menu_items as $url => $item): ?>
            <a href="<?php echo $url; ?>"><?php echo $item['icon'] . ' ' . $item['name']; ?></a>
        <?php endforeach; ?>
    </div>

    <div class="content">
        <h1>Settings for <?php echo htmlspecialchars($user['full_name']); ?></h1>
        
        <?php if ($message): ?>
            <p style="color: green; font-weight: bold;"><?php echo $message; ?></p>
        <?php endif; ?>

        <h3>Theme Customization</h3>
        <p>Current Theme: **<?php echo ucfirst($theme_class); ?>**</p>
        
        <form method="post" action="settings.php">
            <label for="theme_color">Select a New Theme:</label>
            <select name="theme_color" id="theme_color">
                <option value="default" <?php echo ($theme_class == 'default') ? 'selected' : ''; ?>>Default (New Colors)</option>
                <option value="blue" <?php echo ($theme_class == 'blue') ? 'selected' : ''; ?>>Blue</option>
                <option value="green" <?php echo ($theme_class == 'green') ? 'selected' : ''; ?>>Green</option>
                <option value="pastel" <?php echo ($theme_class == 'pastel') ? 'selected' : ''; ?>>Pastel (Palette Theme)</option>
                <option value="monochrome" <?php echo ($theme_class == 'monochrome') ? 'selected' : ''; ?>>Monochrome (Black/White)</option>
            </select>
            <br><br>
            <input type="submit" value="Save Theme Settings">
        </form>

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
<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once 'user_db.php'; 


$searchKeyword = '';
if (isset($_GET['search'])) {
    $searchKeyword = mysqli_real_escape_string($conn, $_GET['search']);
    $sql = "SELECT professor_id, name, email, department FROM professors WHERE name LIKE '%$searchKeyword%' OR email LIKE '%$searchKeyword%' OR department LIKE '%$searchKeyword%'";
} else {
    $sql = "SELECT professor_id, name, email, department FROM professors";
}


$result = $conn->query($sql);

$facultyMembers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $facultyMembers[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professors</title>
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
        h2 {
            color: #4D3B8C; 
            margin-bottom: 20px;
            text-align: center;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 98%;
            max-width: 1920px;
            margin: 20px auto 0 auto;
        }
        .top-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .top-actions a {
            text-decoration: none;
            color: #BBADD9; 
            padding: 8px 15px;
            border: 1px solid #4D3B8C;
            border-radius: 4px;
            background-color: #4D3B8C; 
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        .top-actions a:hover {
            background-color: #3B2D59; 
            color: #BBADD9;
            border-color: #3B2D59; 
        }
        .search-container {
            margin-bottom: 20px;
            text-align: center;
        }
        .search-form {
            display: inline-flex; 
            align-items: center; 
            gap: 5px;
            justify-content: center;
        }
        .search-input {
            padding: 10px;
            border: 1px solid #7F6DA6;
            border-radius: 4px;
            width: 100vh; 
            font-size: 1em;
            color: #3B2D59; 
            background-color: #f8f9fa; 
        }
        .search-button {
            padding: 1%;
            background-color: #4D3B8C;
            color: #BBADD9; 
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 10px; 
            transition: background-color 0.3s ease;
        }
        .search-button:hover {
            background-color: #3B2D59; 
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
        }
        tr:nth-child(even) {
            background-color: #f0e6ff; 
        }
        .add-new-link {
            display: inline-block;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Faculty Monitoring</h2>
        <div class="top-actions">
            <div>
                <a href="faculty_info.php" class="add-new-link" style="background-color: #7F6DA6; color: #BBADD9; border-color: #7F6DA6;">View All Faculty Members</a>
                <a href="add_faculty.php" class="add-new-link">Add Professor Schedule</a>
            </div>
            <a href="admin.php" style="background-color: #4D3B8C; color: #BBADD9; border-color: #4D3B8C;">Back to Admin Dashboard</a>
        </div>

        <div class="search-container">
            <form method="get" class="search-form">
                <button type="submit" class="search-button">Search</button>
                <input type="text" name="search" placeholder="Search by Name, Email, or Department" class="search-input">
            </form>
        </div>

        <?php if (empty($facultyMembers)): ?>
            <p style="color: #3B2D59;">No faculty members found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($facultyMembers as $faculty): ?>
                        <tr>
                            <td><?php echo $faculty['professor_id']; ?></td>
                            <td><?php echo $faculty['name']; ?></td>
                            <td><?php echo $faculty['email']; ?></td>
                            <td><?php echo $faculty['department']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
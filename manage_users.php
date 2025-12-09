<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'user_db.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #BBADD9; 
            margin: 20px;
            color: #3B2D59; 
        }
        h2 {
            color: #4D3B8C;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px #7F6DA6;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #7F6DA6; 
            font-weight: bold;
            color: #BBADD9;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .actions a {
            display: inline-block;
            margin-right: 10px;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .edit-button {
            background-color: #4D3B8C; 
            color: white;
        }
        .edit-button:hover {
            background-color: #3B2D59; 
        }
        .delete-button {
            background-color: #D98FCC; 
            color: white;
        }
        .delete-button:hover {
            background-color: #BBADD9; 
            color: #3B2D59;
        }
        .add-new-link, .back-to-admin-link {
            display: inline-block;
            padding: 10px 15px;
            text-decoration: none;
            background-color: #4D3B8C; 
            color: white;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }
        .back-to-admin-link {
            background-color: #7F6DA6; 
        }
        .add-new-link:hover {
            background-color: #3B2D59; 
        }
        .back-to-admin-link:hover {
            background-color: #BBADD9; 
            color: #3B2D59;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a, .pagination span {
            padding: 8px 12px;
            margin: 0 5px;
            border: 1px solid #7F6DA6; 
            border-radius: 4px;
            text-decoration: none;
            color: #3B2D59;
        }
        .pagination a:hover {
            background-color: #BBADD9; 
            color: #3B2D59;
        }
        .pagination .current {
            background-color: #D98FCC; 
            color: white;
            border-color: #D98FCC;
        }
        .search-bar {
            margin-bottom: 20px;
            text-align: right;
        }
        .search-bar input[type="text"] {
            padding: 8px;
            border: 1px solid #7F6DA6; 
            border-radius: 4px;
            color: #3B2D59;
        }
        .search-bar button {
            padding: 8px 12px;
            background-color: #4D3B8C; 
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background-color: #3B2D59; 
        }
    </style>
</head>
<body>
    <h2>
        Manage Users
        <a href="admin.php" class="back-to-admin-link">Back to Admin</a>
    </h2>

    <div class="search-bar">
        <form method="get">
            <input type="text" name="search" placeholder="Search users...">
            <button type="submit">Search</button>
        </form>
    </div>

    <?php
    if ($conn) {
        if (isset($_SESSION['message'])) {
            echo '<p class="message success">' . $_SESSION['message'] . '</p>';
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error'])) {
            echo '<p class="message error">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }

        $search_term = isset($_GET['search']) ? $_GET['search'] : '';
        $where_clause = '';
        if (!empty($search_term)) {
            $search_term = $conn->real_escape_string($search_term);
            $where_clause = "WHERE name LIKE '%$search_term%' OR email LIKE '%$search_term%' OR role LIKE '%$search_term%'";
        }

        $limit = 10; 
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        
        if ($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $limit;

        $sql_count = "SELECT COUNT(*) FROM user $where_clause";
        $total_result = $conn->query($sql_count);
        $total_rows = $total_result->fetch_row()[0];
        $total_pages = ceil($total_rows / $limit);

        $sql = "SELECT id, name, email, role FROM user";
        if (!empty($where_clause)) {
            $sql .= " " . $where_clause;
        }
        $sql .= " LIMIT $limit OFFSET $offset";
        $result = $conn->query($sql);

        $users = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }

        if (!empty($users)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $single_user): ?>
                        <tr>
                            <td><?= $single_user['id']; ?></td>
                            <td><?= htmlspecialchars($single_user['name']); ?></td>
                            <td><?= htmlspecialchars($single_user['email']); ?></td>
                            <td><?= htmlspecialchars($single_user['role']); ?></td>
                            <td class="actions">
                                <a href="edit_user.php?id=<?= $single_user['id']; ?>" class="edit-button">Edit</a>
                                <a href="delete_user.php?id=<?= $single_user['id']; ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?><?= !empty($search_term) ? '&search=' . urlencode($search_term) : '' ?>">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?><?= !empty($search_term) ? '&search=' . urlencode($search_term) : '' ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?><?= !empty($search_term) ? '&search=' . urlencode($search_term) : '' ?>">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <p>No users found.</p>
        <?php endif;

        $conn->close();

    } else {
        echo "Database connection failed. Check 'user_db.php' for errors.";
    }
    ?>

</body>
</html>
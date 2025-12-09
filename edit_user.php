<?php
session_start();
require_once 'user_db.php';

$colors = ['4D3B8C', '#BBADD9', '#7F6DA6', '#3B2D59', '#D98FCC'];
$primaryColor = $colors[0];
$secondaryColor = $colors[1];
$accentColor = $colors[4]; 

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT id, name, email, role FROM user WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        die("User not found.");
    }
} else {
    die("User ID not provided.");
}

if (isset($_POST['edit_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $id = $_POST['id'];

    $stmt = $conn->prepare("UPDATE user SET name = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $role, $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "User updated successfully!";
        header("Location: manage_users.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating user: " . $stmt->error;
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: <?= $secondaryColor ?>;
            color: <?= $primaryColor ?>;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }

        h2 {
            color: <?= $primaryColor ?>;
            margin-bottom: 20px;
        }

        form {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-width: 90%;
        }

        div {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: <?= $colors[2] ?>;
        }

        input[type="text"],
        input[type="email"],
        select {
            width: calc(100% - 12px);
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg fill=\"<?= str_replace('#', '%23', $colors[2]) ?>\" viewBox=\"0 0 24 24\"><path d=\"M7 10l5 5 5-5z\"/></svg>');
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 16px;
            padding-right: 30px;
        }

        button[type="submit"] {
            background-color: <?= $accentColor ?>;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: <?= $colors[3] ?>;
        }

        p[style*="color: red;"] {
            color: red;
            margin-bottom: 10px;
            font-weight: bold;
        }

        p a {
            color: <?= $colors[2] ?>;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h2>Edit User</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?= $_SESSION['error']; ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="id" value="<?= $user['id']; ?>">
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
        </div>
        <div>
            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="user" <?= ($user['role'] === 'user' ? 'selected' : ''); ?>>User</option>
                <option value="admin" <?= ($user['role'] === 'admin' ? 'selected' : ''); ?>>Admin</option>
            </select>
        </div>
        <button type="submit" name="edit_user">Save Changes</button>
    </form>

    <p><a href="manage_users.php">Back to Manage Users</a></p>

</body>
</html>
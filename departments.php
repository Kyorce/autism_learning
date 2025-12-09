<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
require_once 'user_db.php';

$message = '';


if (isset($_POST['add_department'])) {
    $new_department_name = $_POST['new_department_name'];

    if (empty($new_department_name)) {
        $message = '<div class="error-message">Department name cannot be empty.</div>';
    } else {
        $sql = "INSERT INTO departments (name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $new_department_name);

        if ($stmt->execute()) {
            $message = '<div class="success-message">Department added successfully!</div>';
        
        } else {
            $message = '<div class="error-message">Error adding department: ' . $stmt->error . '</div>';
        }
        $stmt->close();
    }
}


if (isset($_POST['save_department'])) {
    $edit_department_id = $_POST['edit_department_id'];
    $edit_department_name = $_POST['edit_department_name'];

    if (empty($edit_department_name)) {
        $message = '<div class="error-message">Department name cannot be empty.</div>';
    } else {
        $sql_update = "UPDATE departments SET name = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $edit_department_name, $edit_department_id);

        if ($stmt_update->execute()) {
            $message = '<div class="success-message">Department updated successfully!</div>';
    
        } else {
            $message = '<div class="error-message">Error updating department: ' . $stmt_update->error . '</div>';
        }
        $stmt_update->close();
    }
}

$departments = [];
$sql_select = "SELECT id, name FROM departments ORDER BY name";
$result_select = $conn->query($sql_select);
if ($result_select && $result_select->num_rows > 0) {
    while ($row = $result_select->fetch_assoc()) {
        $departments[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments Management</title>
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
            padding: 20px;
        }

        .container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px #7F6DA6;
            padding: 30px;
            max-width: 800px;
            margin: 20px auto;
        }

        h2 {
            font-size: 34px;
            text-align: center;
            margin-bottom: 20px;
            color: #4D3B8C;
        }

        h3 {
            color: #555;
            margin-top: 15px;
            margin-bottom: 10px;
        }

        #department-list {
            list-style-type: none;
            padding: 0;
        }

        #department-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #department-list li:last-child {
            border-bottom: none;
        }

        #department-list li .department-name {
            flex-grow: 1;
        }

        #department-list li .actions {
            display: flex;
            gap: 10px;
        }

        #department-list li .actions button {
            padding: 8px 12px;
            background: #4D3B8C;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.4s;
        }

        #department-list li .actions button:hover {
            background: #845cdb;
        }

        #department-list li .actions button.delete {
            background: #dc3545;
        }

        #department-list li .actions button.delete:hover {
            background: #c82333;
        }

        #add-department-form,
        #edit-department-form {
            margin-top: 20px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        #add-department-form div,
        #edit-department-form div {
            margin-bottom: 15px;
        }

        #add-department-form label,
        #edit-department-form label {
            display: block;
            margin-bottom: 5px;
            color: #3B2D59;
            font-weight: 500;
            font-size: 16px;
        }

        #add-department-form input[type="text"],
        #edit-department-form input[type="text"] {
            width: 100%;
            padding: 10px;
            background: #f3f3f3;
            border-radius: 6px;
            border: 1px solid #7F6DA6;
            outline: none;
            font-size: 16px;
            color: #555;
        }

        #add-department-form button[type="submit"],
        #edit-department-form button[type="submit"],
        #edit-department-form button[type="button"] {
            width: auto;
            padding: 10px 20px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.4s;
            margin-top: 10px;
            margin-right: 10px;
        }

        #edit-department-form button[type="button"]#cancel-edit {
            background: #6c757d;
        }

        #add-department-form button[type="submit"]:hover,
        #edit-department-form button[type="submit"]:hover,
        #edit-department-form button[type="button"]:hover {
            opacity: 0.9;
        }

        .back-to-admin {
            margin-top: 20px;
            text-align: center;
        }

        .back-to-admin button {
            padding: 10px 20px;
            background: #6c757d; /* Gray button */
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.4s;
        }

        .back-to-admin button:hover {
            background: #5a6268;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
            border: 1px solid red;
            padding: 10px;
            border-radius: 5px;
            background-color: #ffe6e6;
            text-align: center;
        }

        .success-message {
            color: green;
            margin-bottom: 10px;
            border: 1px solid green;
            padding: 10px;
            border-radius: 5px;
            background-color: #e6ffe6;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Departments Management</h2>
    <?php echo $message; ?>
    <div>
        <h3>Existing Departments</h3>
        <ul id="department-list">
            <?php foreach ($departments as $dept): ?>
                <li>
                    <span class="department-name"><?php echo htmlspecialchars($dept['name']); ?></span>
                    <div class="actions">
                        <button onclick="showEditForm(<?php echo json_encode($dept); ?>)">Edit</button>
                        <button class="delete" onclick="deleteDepartment(<?php echo $dept['id']; ?>)">Delete</button>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <h3>Add New Department</h3>
        <form id="add-department-form" method="post">
            <div>
                <label for="new-department-name">Department Name:</label>
                <input type="text" id="new-department-name" name="new_department_name" required>
            </div>
            <button type="submit" name="add_department">Add Department</button>
        </form>
        <h3>Edit Department</h3>
        <form id="edit-department-form" style="display:none;" method="post">
            <div>
                <label for="edit-department-id">Department ID:</label>
                <input type="text" id="edit-department-id" name="edit_department_id" readonly>
            </div>
            <div>
                <label for="edit-department-name">Department Name:</label>
                <input type="text" id="edit-department-name" name="edit_department_name" required>
            </div>
            <button type="submit" name="save_department">Save Changes</button>
            <button type="button" id="cancel-edit">Cancel</button>
        </form>
    </div>

    <div class="back-to-admin">
        <button onclick="window.location.href='admin.php'">Back to Admin</button>
    </div>

    <script>
        const departmentList = document.getElementById('department-list');
        const addDepartmentForm = document.getElementById('add-department-form');
        const editDepartmentForm = document.getElementById('edit-department-form');
        const editDepartmentIdInput = document.getElementById('edit-department-id');
        const editDepartmentNameInput = document.getElementById('edit-department-name');
        const cancelEditButton = document.getElementById('cancel-edit');

        function showEditForm(department) {
            editDepartmentIdInput.value = department.id;
            editDepartmentNameInput.value = department.name;
            editDepartmentForm.style.display = 'block';
        }

        function hideEditForm() {
            editDepartmentForm.style.display = 'none';
        }

        cancelEditButton.addEventListener('click', hideEditForm);

        function deleteDepartment(idToDelete) {
            if (confirm('Are you sure you want to delete this department?')) {
                window.location.href = 'delete_department.php?id=' + idToDelete;
            }
        }

     
    </script>
</div>

</body>
</html>
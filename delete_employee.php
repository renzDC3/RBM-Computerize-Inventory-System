<?php
require_once 'session_config.php'; 
require 'config.php';

if (isset($_POST['Id']) && !empty($_POST['Id'])) {
    $id = intval($_POST['Id']); 

    $con->begin_transaction();

    try {
        $stmt = $con->prepare("SELECT Id, first_name, last_name, date_joined, Username, Password, two_factor_secret 
                                FROM users WHERE Id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("No user found with ID $id");
        }

        $row = $result->fetch_assoc();
        $stmt->close();

        $stmt = $con->prepare("INSERT INTO delete_employee_history 
            (Id, first_name, last_name, date_joined, Username, Password, two_factor_secret) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss",
            $row['Id'],
            $row['first_name'],
            $row['last_name'],
            $row['date_joined'],
            $row['Username'],
            $row['Password'],
            $row['two_factor_secret']
        );
        $stmt->execute();
        $stmt->close();

        $stmt = $con->prepare("DELETE FROM users WHERE Id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $con->commit();

        header("Location: employees.php?msg=deleted_successfully");
        exit;

    } catch (Exception $e) {
        $con->rollback();
        echo "Error deleting employee: " . $e->getMessage();
    }

} else {
    echo "Invalid request.";
}
?><?php
require_once 'session_config.php'; 
require 'config.php';

if (isset($_POST['Id']) && !empty($_POST['Id'])) {
    $id = intval($_POST['Id']); 

    $con->begin_transaction();

    try {
        // Fetch user data
        $stmt = $con->prepare("SELECT Id, first_name, last_name, date_joined, Username, Password, role, two_factor_secret 
                                FROM users WHERE Id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("No user found with ID $id");
        }

        $row = $result->fetch_assoc();
        $stmt->close();

        // Insert into delete_employee_history
        $stmt = $con->prepare("INSERT INTO delete_employee_history 
            (Id, first_name, last_name, date_joined, Username, Password, two_factor_secret) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss",
            $row['Id'],
            $row['first_name'],
            $row['last_name'],
            $row['date_joined'],
            $row['Username'],
            $row['Password'],
            $row['two_factor_secret']
        );
        $stmt->execute();
        $stmt->close();

        // Delete user
        $stmt = $con->prepare("DELETE FROM users WHERE Id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Insert into system_log
        $user_id = $_SESSION['user_id']; // currently logged in admin/user performing deletion
        $username = $_SESSION['username'];
        $user_role = $_SESSION['role'];
        $action_type = 'Delete Employee';
        $description = "Deleted employee: ID={$row['Id']}, Name={$row['first_name']} {$row['last_name']}, Username={$row['Username']}, Role={$row['role']}, Joined={$row['date_joined']}";
        $module = 'Employees';
        $submodule = 'Delete Employee';
        $result_log = 'Success';
        $date = date('Y-m-d');
        $time = date('H:i:s');

        $stmt = $con->prepare("INSERT INTO system_log 
            (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssssss",
            $user_id,
            $username,
            $user_role,
            $action_type,
            $description,
            $module,
            $submodule,
            $result_log,
            $date,
            $time
        );
        $stmt->execute();
        $stmt->close();

        $con->commit();

        header("Location: employees.php?msg=deleted_successfully");
        exit;

    } catch (Exception $e) {
        $con->rollback();
        echo "Error deleting employee: " . $e->getMessage();
    }

} else {
    echo "Invalid request.";
}
?>


<?php
require_once 'session_config.php';
require_once 'csrf.php';
require 'config.php';

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

$query = "SELECT id, password FROM users";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

$updated = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $userId = $row['id'];
    $plainPassword = $row['password'];

    // Skip if already hashed (common heuristic: check if it starts with $2y$)
    if (preg_match('/^\$2y\$/', $plainPassword)) {
        continue;
    }

    // Hash the plaintext password
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    // Update the record
    $updateQuery = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = mysqli_prepare($con, $updateQuery);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $hashedPassword, $userId);
        if (mysqli_stmt_execute($stmt)) {
            $updated++;
        }
        mysqli_stmt_close($stmt);
    }
}

echo "<p>✅ Password hashing complete. $updated user(s) updated.</p>";
?>

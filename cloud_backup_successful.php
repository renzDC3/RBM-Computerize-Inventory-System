<?php
require_once 'session_config.php'; 
require 'config.php';

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit;
}

date_default_timezone_set('Australia/Perth');

// ==============================
// 1️⃣ Dropbox Access Token
// ==============================
define('DROPBOX_ACCESS_TOKEN', $DROPBOX_ACCESS_TOKEN);

// ==============================
// 2️⃣ Database Backup Settings
// ==============================
$backupDir = __DIR__ . '/temp_backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$date = date('Y-m-d_H-i-s');
$backupFileName = "db_backup_{$date}.sql";
$backupFilePath = $backupDir . '/' . $backupFileName;

// ==============================
// 3️⃣ Generate .sql backup
// ==============================
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'inventory_and_sales_system_database';
$mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';

$mysqldump = sprintf(
    '%s --user=%s --password=%s --host=%s %s > %s',
    escapeshellcmd($mysqldumpPath),
    escapeshellarg($dbUser),
    escapeshellarg($dbPass),
    escapeshellarg($dbHost),
    escapeshellarg($dbName),
    escapeshellarg($backupFilePath)
);

exec($mysqldump, $output, $returnVar);

if ($returnVar !== 0) {
    echo "<script>alert('Failed to create database backup. Please check configuration and permissions.'); window.location.href='report.php';</script>";
    exit;
}

// ==============================
// 4️⃣ Upload to Dropbox
// ==============================
$fileContent = file_get_contents($backupFilePath);
$dropboxPath = '/backups/' . $backupFileName;

$ch = curl_init('https://content.dropboxapi.com/2/files/upload');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . DROPBOX_ACCESS_TOKEN,
    'Content-Type: application/octet-stream',
    'Dropbox-API-Arg: ' . json_encode([
        'path' => $dropboxPath,
        'mode' => 'add',
        'autorename' => true,
        'mute' => false
    ])
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// ==============================
// 5️⃣ Handle Results + Log Action
// ==============================
$userId = $_SESSION['id'];
$username = $_SESSION['valid'];

// 🔍 Fetch user role from the users table
$roleQuery = $con->prepare("SELECT role FROM users WHERE Id = ?");
$roleQuery->bind_param("i", $userId);
$roleQuery->execute();
$roleResult = $roleQuery->get_result();
$userRole = ($roleResult->num_rows > 0) ? $roleResult->fetch_assoc()['role'] : 'Unknown';
$roleQuery->close();

$module = 'Backup';
$submodule = 'Upload to Cloud';
$actionType = 'Upload to Cloud';
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

if ($httpCode == 200) {
    $data = json_decode($response, true);
    $fileId = $data['id'];

    // ✅ Record successful upload in backups table
    $stmt = $con->prepare("INSERT INTO backups (file_name, file_id, uploaded_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $backupFileName, $fileId);
    $stmt->execute();

    // ✅ Log success in system_log
    $description = "Database backup '$backupFileName' uploaded to Dropbox successfully.";
    $result = "Success";
    $logStmt = $con->prepare("INSERT INTO system_log (user_id, username, user_role, action_type, description, module, submodule, result, date, time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $logStmt->bind_param("isssssssss", $userId, $username, $userRole, $actionType, $description, $module, $submodule, $result, $currentDate, $currentTime);
    $logStmt->execute();
    $logStmt->close();

    unlink($backupFilePath);

    echo "<script>alert('Database backup successfully uploaded to Dropbox!'); window.location.href='cloud.php';</script>";
} else {
    $errorMsg = htmlspecialchars($response);
    // ❌ Log failure in system_log
    $description = "Dropbox upload failed for '$backupFileName'. Error: $errorMsg";
    $result = "Failed";
    $logStmt = $con->prepare("INSERT INTO system_log (user_id, username, user_role, action_type, description, module, submodule, result, date, time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $logStmt->bind_param("isssssssss", $userId, $username, $userRole, $actionType, $description, $module, $submodule, $result, $currentDate, $currentTime);
    $logStmt->execute();
    $logStmt->close();

    echo "<script>alert('Dropbox upload failed: {$errorMsg}'); window.location.href='cloud.php';</script>";
}
?>

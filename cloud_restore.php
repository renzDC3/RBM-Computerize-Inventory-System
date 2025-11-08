<?php
require_once 'session_config.php'; 
require 'config.php';

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit;
}

// Dropbox access token
define('DROPBOX_ACCESS_TOKEN', $DROPBOX_ACCESS_TOKEN);

// ===========================
// 1️⃣ SELECT BACKUP TO RESTORE
// ===========================
if (!isset($_GET['file'])) {
    $message = "❌ No file specified for restore.";
    $redirect = "backups.php";
    echo "<script>alert('$message'); window.location.href='$redirect';</script>";
    exit;
}

$dropboxFilePath = $_GET['file']; 

// ===========================
// 2️⃣ DOWNLOAD FILE FROM DROPBOX
// ===========================
$tempDir = __DIR__ . '/temp_restore';
if (!is_dir($tempDir)) mkdir($tempDir, 0755, true);

$tempFilePath = $tempDir . '/' . basename($dropboxFilePath);

$ch = curl_init('https://content.dropboxapi.com/2/files/download');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . DROPBOX_ACCESS_TOKEN,
    'Dropbox-API-Arg: ' . json_encode(['path' => $dropboxFilePath])
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$fileContent = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    $message = "❌ Dropbox download failed (HTTP $httpCode).";
    $redirect = "backups.php";
    echo "<script>alert('$message'); window.location.href='$redirect';</script>";
    exit;
}

file_put_contents($tempFilePath, $fileContent);

// ===========================
// 3️⃣ RESTORE INTO DATABASE
// ===========================
$mysqli = $con;

$sql = file_get_contents($tempFilePath);
if ($sql === false) {
    unlink($tempFilePath);
    $message = "❌ Failed to read downloaded SQL file.";
    $redirect = "backups.php";
    echo "<script>alert('$message'); window.location.href='$redirect';</script>";
    exit;
}

$mysqli->query("SET foreign_key_checks = 0;");

if ($mysqli->multi_query($sql)) {
    do {
        $mysqli->next_result();
    } while ($mysqli->more_results());
    $result = "Success";
    $message = "✅ Database restored successfully!";
} else {
    $result = "Failed: " . $mysqli->error;
    $message = "⚠️ Errors occurred during restore: " . addslashes($mysqli->error);
}

$mysqli->query("SET foreign_key_checks = 1;");
unlink($tempFilePath);

// ===========================
// 4️⃣ LOG TO SYSTEM_LOG
// ===========================
$user_id = $_SESSION['user_id']; // Make sure your session has this
$username = $_SESSION['username'];
$user_role = $_SESSION['role'];

$module = 'Backup';
$submodule = 'Restore from Cloud';
$action_type = 'Restore from Cloud';
$description = "Restored database from file: " . basename($dropboxFilePath);
$date = date('Y-m-d');
$time = date('H:i:s');

$log_stmt = $mysqli->prepare("INSERT INTO system_log (user_id, username, user_role, action_type, description, module, submodule, result, date, time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$log_stmt->bind_param("isssssssss", $user_id, $username, $user_role, $action_type, $description, $module, $submodule, $result, $date, $time);
$log_stmt->execute();
$log_stmt->close();

// ===========================
// 5️⃣ SHOW ALERT & REDIRECT
// ===========================
$redirect = "cloud.php"; 
echo "<script>
    alert('$message');
    window.location.href = '$redirect';
</script>";
exit;
?>

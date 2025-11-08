<?php
require_once 'session_config.php'; 
require 'config.php';

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit;
}

date_default_timezone_set('Australia/Perth');

// Dropbox API setup
$dropboxToken = $DROPBOX_ACCESS_TOKEN;
$dropboxFolder = '/backups';

// Fetch file list from Dropbox
$ch = curl_init('https://api.dropboxapi.com/2/files/list_folder');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $dropboxToken,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'path' => $dropboxFolder,
    'recursive' => false
]));  
$response = curl_exec($ch);
curl_close($ch);

$dropboxFiles = json_decode($response, true);

// ✅ Sort by server_modified descending (newest to oldest)
if (isset($dropboxFiles['entries'])) {
    usort($dropboxFiles['entries'], function ($a, $b) {
        if ($a['.tag'] !== 'file' || $b['.tag'] !== 'file') return 0;
        return strtotime($b['server_modified']) - strtotime($a['server_modified']);
    });
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="styles/cloudStyle.css">
</head>
<body>

<div class="topnav" id="myTopnav">
  <a class="image"><img src="images/rbm_tex.jpg" style="width: 50px; height: 15px"></a>

  <?php if ($_SESSION['valid'] === 'Admin' or $_SESSION['valid'] === 'Manager') { ?>
      <a href="dashboard.php">Dashboard</a>           
  <?php } ?>

  <a href="products.php">Products</a>

  <?php if ($_SESSION['valid'] != 'Manager') { ?>
    <a href="sales.php">Sales</a>
    <a href="services.php">Services</a>
  <?php } ?> 

  <?php if ($_SESSION['valid'] === 'Admin'  or $_SESSION['valid'] === 'Manager') { ?>
      <a href="history.php">History</a>
      <a href="employees.php">Employees</a>
      <a href="suppliers.php">Suppliers</a>
      <a href="report.php">Report</a>
      <a class="active" href="cloud.php">Backup</a>
  <?php } ?>

  <?php if ($_SESSION['valid'] === 'Admin') { ?>

  <a href="system_log.php">System Log</a>

  <?php } ?>

  <a class="logout" href="logout.php">Logout</a>

  <!-- Hamburger icon -->
  <a href="javascript:void(0);" class="icon" onclick="toggleNav()">&#9776;</a>
</div>

<div class="checkout"> <h3 style="text-align: center">Upload Database Backup to Dropbox?</h3> <hr> <a href="cloud_backup_successful.php">Yes</a> <a href="dashboard.php" style="float:right; margin-top:-5px">No</a> <br> </div>

<div class="checkout">
  <h3 style="text-align:center">Restore Available Backups</h3>

  <!-- Sorting Dropdown -->
  <div style="text-align: right; margin-bottom: 10px;">
    <label for="sortSelect"><strong>Sort by:</strong></label>
    <select id="sortSelect">
      <option value="newest">Newest to Oldest</option>
      <option value="oldest">Oldest to Newest</option>
    </select>
  </div>

  <div class="productsTable">
    <table id="backupTable">
      <thead>
        <tr><th>File</th><th>Date</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php
        if (isset($dropboxFiles['entries'])) {
            foreach ($dropboxFiles['entries'] as $file) {
                if ($file['.tag'] === 'file') {
                    $fileName = htmlspecialchars($file['name']);
                    $fileDate = date('Y-m-d H:i:s', strtotime($file['server_modified']));
                    $encodedPath = urlencode($file['path_lower']);
                    echo "<tr>
                            <td>{$fileName}</td>
                            <td>{$fileDate}</td>
                            <td><a href='cloud_restore.php?file={$encodedPath}'>Restore</a></td>
                          </tr>";
                }
            }
        } else {
            echo '<tr><td colspan="3">No backups found in Dropbox.</td></tr>';
        }
        ?>
        </tbody>
    </table>
  </div>
</div>

<script>
// Sorting Function
document.getElementById('sortSelect').addEventListener('change', function() {
  const table = document.getElementById('backupTable').getElementsByTagName('tbody')[0];
  const rows = Array.from(table.rows);
  const sortType = this.value;

  rows.sort((a, b) => {
    const dateA = new Date(a.cells[1].innerText);
    const dateB = new Date(b.cells[1].innerText);
    return sortType === 'newest' ? dateB - dateA : dateA - dateB;
  });

  // Re-append rows in new order
  rows.forEach(row => table.appendChild(row));
});
</script>

<script>
function toggleNav() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}
</script>

</body>
</html>

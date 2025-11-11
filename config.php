<?php
// --- Database connection ---
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = '4530883_rbm';

$con = new mysqli($host, $user, $pass, $db);

if ($con->connect_error) {
    die('Database connection failed: ' . $con->connect_error);
}

$con->set_charset('utf8mb4');

/*
// --- Dropbox App credentials ---
$DROPBOX_APP_KEY = 'f5t42j6i390xvxy';
$DROPBOX_APP_SECRET = 'i3utpwikwncw3v3';
$DROPBOX_REFRESH_TOKEN = 'HHQaUQUEUX8AAAAAAAAAAYkwma4ghfnMIIxtQ_-FJpaLAy4ZOUm9we7sl4jGEhfr';

// --- Function to get a fresh access token ---
function getDropboxAccessToken($appKey, $appSecret, $refreshToken) {
    $url = "https://api.dropboxapi.com/oauth2/token";
    $data = [
        "grant_type" => "refresh_token",
        "refresh_token" => $refreshToken,
        "client_id" => $appKey,
        "client_secret" => $appSecret
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die('cURL error: ' . curl_error($ch));
    }
    curl_close($ch);

    $json = json_decode($response, true);
    if (isset($json['access_token'])) {
        return $json['access_token'];
    } else {
        die('Failed to get Dropbox access token: ' . $response);
    }
}

// --- Get a valid Dropbox token dynamically ---
$DROPBOX_ACCESS_TOKEN = getDropboxAccessToken($DROPBOX_APP_KEY, $DROPBOX_APP_SECRET, $DROPBOX_REFRESH_TOKEN);
?>
*/
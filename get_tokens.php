<?php
// --- Dropbox App credentials ---
$APP_KEY = 'f5t42j6i390xvxy';
$APP_SECRET = 'i3utpwikwncw3v3';
$AUTH_CODE = 'fAMqh2RO_1YAAAAAAAAAH2GzrhQewWVhgebEviRM3V0';  // from the redirect step
$REDIRECT_URI = 'http://localhost/system_new/index.php';  // must match your app settings

// --- Build POST data ---
$data = [
    'code' => $AUTH_CODE,
    'grant_type' => 'authorization_code',
    'client_id' => $APP_KEY,
    'client_secret' => $APP_SECRET,
    'redirect_uri' => $REDIRECT_URI
];

// --- Send request to Dropbox token endpoint ---
$ch = curl_init('https://api.dropboxapi.com/oauth2/token');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    die('cURL error: ' . curl_error($ch));
}
curl_close($ch);

// --- Parse response ---
$json = json_decode($response, true);

echo "<pre>";
print_r($json);
echo "</pre>";
?>

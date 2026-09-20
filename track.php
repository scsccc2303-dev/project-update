<?php

header("Content-Type: application/json");

$BOT_TOKEN = "8488176092:AAE4pPzvUTaXGg5S7lpR8IJThsqd4Fc1QHE";
$CHAT_ID   = "1326920917";

$input = json_decode(file_get_contents("php://input"), true) ?: [];

$event  = $input["event"] ?? "UNKNOWN";
$device = $input["device"] ?? "Unknown";
$page   = $input["page"] ?? "Unknown";
$time   = $input["time"] ?? date("c");

$country = "Unknown";

/*
|--------------------------------------------------------------------------
| COUNTRY
|--------------------------------------------------------------------------
| If your site is behind Cloudflare, this usually gives the country code.
*/

if (!empty($_SERVER["HTTP_CF_IPCOUNTRY"])) {
    $country = $_SERVER["HTTP_CF_IPCOUNTRY"];
}

/*
|--------------------------------------------------------------------------
| MESSAGE
|--------------------------------------------------------------------------
*/

$message =
    "📄 Document Activity\n\n" .
    "Event: " . $event . "\n" .
    "Device: " . $device . "\n" .
    "Country: " . $country . "\n" .
    "Time: " . $time . "\n" .
    "Page: " . $page;

/*
|--------------------------------------------------------------------------
| SEND TO TELEGRAM
|--------------------------------------------------------------------------
*/

$url = "https://api.telegram.org/bot" . $BOT_TOKEN . "/sendMessage";

$data = [
    "chat_id" => $CHAT_ID,
    "text" => $message
];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo json_encode([
    "success" => $httpCode === 200,
    "http_code" => $httpCode,
    "curl_error" => $error
]);
?>
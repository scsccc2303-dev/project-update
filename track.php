<?php
header("Content-Type: application/json");

// 1. GET TOKENS FROM VERCEL ENVIRONMENT VARIABLES (Secure!)
// If not set, it falls back to the hardcoded ones temporarily (CHANGE THIS LATER!)
$BOT_TOKEN = getenv('TELEGRAM_BOT_TOKEN') ?: "8798836938:AAHKnOtS2Rf7jp_0Z_KASO0JJONfyPPkaGY";
$CHAT_ID   = getenv('TELEGRAM_CHAT_ID') ?: "1326920917";

// 2. HANDLE BOTH GET (Link clicks) AND POST (JSON data)
$input = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true) ?: [];
} else {
    $input = $_GET; // Fallback for when you just click the link in a browser
}

$event  = $input["event"] ?? "LINK_CLICKED";
$device = $input["device"] ?? "Unknown";
$page   = $input["page"] ?? $_SERVER['HTTP_REFERER'] ?? "Unknown";
$time   = $input["time"] ?? date("Y-m-d H:i:s");
$ip     = $_SERVER["HTTP_CF_CONNECTING_IP"] ?? $_SERVER["REMOTE_ADDR"] ?? "Unknown";
$country = $_SERVER["HTTP_CF_IPCOUNTRY"] ?? "Unknown";

// 3. BUILD THE MESSAGE
$message = 
    "📄 *Document Activity*\n\n" .
    "🔹 *Event:* " . $event . "\n" .
    "🔹 *Device:* " . $device . "\n" .
    "🔹 *Country:* " . $country . "\n" .
    "🔹 *IP:* " . $ip . "\n" .
    "🔹 *Time:* " . $time . "\n" .
    "🔹 *Page:* " . $page;

// 4. SEND TO TELEGRAM
$url = "https://api.telegram.org/bot" . $BOT_TOKEN . "/sendMessage";

$data = [
    "chat_id" => $CHAT_ID,
    "text" => $message,
    "parse_mode" => "Markdown" // Makes the message look nice and bold in Telegram
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
// Add this to help debug SSL issues on some servers
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 

$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 5. RETURN CLEAR RESULTS SO YOU CAN DEBUG
echo json_encode([
    "success" => ($httpCode === 200),
    "http_code" => $httpCode,
    "telegram_response" => $response,
    "curl_error" => $error,
    "debug_info" => "If http_code is 400, check if you sent /start to the bot. If 401, your token is wrong."
], JSON_PRETTY_PRINT);
?>

export default async function handler(req, res) {
// 1. Handle GET (link clicks) and POST requests
const data = req.method === 'POST' ? req.body : req.query;

// 2. Setup Token and ID
const BOT_TOKEN = "8488176892:AAE4pPZvUTaXDgTS7IpR8IJThsqd4Fc1QHE";
const CHAT_ID = "1326328917";

// 3. Get Data
const event = data.event || "LINK_CLICKED";
const device = data.device || "Unknown";
const page = data.page || "Unknown";
const time = new Date().toLocaleString();

// 4. Build Message
const message = ` Document Activity\n\n🔹 Event: ${event}\n🔹 Device: ${device}\n🔹 Time: ${time}\n🔹 Page: ${page}`;

// 5. Send to Telegram - FIX: Added backticks here!
const url = `https://api.telegram.org/bot${BOT_TOKEN}/sendMessage`;

try {
const response = await fetch(url, {
method: 'POST',
headers: { 'Content-Type': 'application/json' },
body: JSON.stringify({
chat_id: CHAT_ID,
text: message,
parse_mode: 'Markdown'
})
});

const result = await response.json();

if (response.ok) {
res.status(200).json({ success: true, telegram_response: result });
} else {
res.status(response.status).json({ success: false, error: result });
}
} catch (error) {
res.status(500).json({ success: false, error: error.message });
}
}

<?php
/**
 * Telegram notifier: reports pending orders to a Telegram chat.
 *
 * Credentials are NO LONGER hardcoded here. They are read from app/config.php
 * (which loads app/secrets.php or environment variables). See
 * app/secrets.example.php.
 */
define("BASEPATH", TRUE);
require $_SERVER["DOCUMENT_ROOT"]."/vendor/autoload.php";
require $_SERVER["DOCUMENT_ROOT"]."/app/init.php";

$telegramBotToken = isset($config["telegram"]["bot_token"]) ? $config["telegram"]["bot_token"] : getenv("TELEGRAM_BOT_TOKEN");
$chatId           = isset($config["telegram"]["chat_id"])   ? $config["telegram"]["chat_id"]   : getenv("TELEGRAM_CHAT_ID");

if (empty($telegramBotToken) || empty($chatId)) {
    error_log("[bot.php] Telegram credentials are not configured; skipping.");
    return;
}

$telegramApiUrl = "https://api.telegram.org/bot" . $telegramBotToken . "/sendMessage";

try {
    // Reuse the shared $conn from app/init.php (no second connection / no secrets).
    $stmt = $conn->prepare("SELECT order_id FROM orders WHERE order_status = :status");
    $stmt->execute(array("status" => "pending"));
    $pendingOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $message = "Pending Orders:\n";
    if (empty($pendingOrders)) {
        $message .= "No pending orders at the moment.";
    } else {
        foreach ($pendingOrders as $order) {
            $message .= "Order ID: " . $order['order_id'] . "\n";
        }
    }

    $ch = curl_init($telegramApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        'chat_id' => $chatId,
        'text'    => $message,
    ));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log("[bot.php] Telegram request failed: " . curl_error($ch));
    }
    curl_close($ch);
} catch (PDOException $e) {
    error_log("[bot.php] Database error: " . $e->getMessage());
}

<?php
/**
 * SECRETS TEMPLATE
 *
 * 1. Copy this file to app/secrets.php
 * 2. Fill in your real values
 * 3. NEVER commit app/secrets.php (it is git-ignored)
 *
 * Alternatively, leave app/secrets.php absent and provide the matching
 * environment variables instead (DB_NAME, DB_HOST, DB_USER, DB_PASS,
 * DB_CHARSET, TELEGRAM_BOT_TOKEN, TELEGRAM_CHAT_ID).
 */
return [
    'db' => [
        'name'    => 'your_db_name',
        'host'    => 'localhost',
        'user'    => 'your_db_user',
        'pass'    => 'your_db_password',
        'charset' => 'utf8mb4',
    ],
    'telegram' => [
        'bot_token' => 'your_telegram_bot_token',
        'chat_id'   => 'your_telegram_chat_id',
    ],
];

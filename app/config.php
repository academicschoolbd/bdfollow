<?php
define('PATH', realpath('.'));
define('SUBFOLDER', false);
define('URL', 'https://bdfollow.com' );
define('STYLESHEETS_URL', '//bdfollow.com' );
date_default_timezone_set('Asia/Dhaka');

/*
 ini_set("display_errors","1");
 error_reporting(E_ERROR);  */

error_reporting(0);

/*
 * Secrets are NO LONGER stored in this file.
 *
 * Provide them in app/secrets.php (git-ignored) by copying
 * app/secrets.example.php, or via environment variables.
 */
$secrets = [];
if (is_file(__DIR__ . '/secrets.php')) {
    $secrets = require __DIR__ . '/secrets.php';
}
if (!is_array($secrets)) {
    $secrets = [];
}

return [
  'db' => [
    'name'    => $secrets['db']['name']    ?? (getenv('DB_NAME')    ?: ''),
    'host'    => $secrets['db']['host']    ?? (getenv('DB_HOST')    ?: 'localhost'),
    'user'    => $secrets['db']['user']    ?? (getenv('DB_USER')    ?: ''),
    'pass'    => $secrets['db']['pass']    ?? (getenv('DB_PASS')    ?: ''),
    'charset' => $secrets['db']['charset'] ?? (getenv('DB_CHARSET') ?: 'utf8mb4'),
  ],
  'telegram' => [
    'bot_token' => $secrets['telegram']['bot_token'] ?? (getenv('TELEGRAM_BOT_TOKEN') ?: ''),
    'chat_id'   => $secrets['telegram']['chat_id']   ?? (getenv('TELEGRAM_CHAT_ID')   ?: ''),
  ],
];

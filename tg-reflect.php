<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/settings.php';

use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

// Set up the connection to the SQLite database
$db_file = __DIR__ . '/prompts.sqlite';
$db = new SQLite3($db_file);

// Set up the Telegram bot

$telegram = new Api($botToken);

// Retrieve a random question from the database
$query = 'SELECT body FROM prompts where vetted = 1 ORDER BY RANDOM() LIMIT 1';
$result = $db->query($query);
$row = $result->fetchArray();
$question = $row['body'];

// Send the question as a message to the Telegram chat using the Telegram Bot API
try {
    $telegram->sendMessage([
        'chat_id' => $chatId,
        'text' => $question,
    ]);
} catch (TelegramSDKException $e) {
    // Handle exceptions from the Telegram Bot API
    echo $e->getMessage();
}

// Close the database connection
$db->close();

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
$query = 'SELECT body,id FROM prompts WHERE (last_used IS NULL OR last_used = 0) ORDER BY RANDOM() LIMIT 1';
$result = $db->query($query);
$row = $result->fetchArray();

// If no question found, select 100 oldest prompts and choose randomly from them
if (! $row) {
  $query = 'SELECT body,id from (SELECT body,id FROM prompts ORDER BY last_used ASC LIMIT 100) order by random() limit 1';
  $result = $db->query($query);
  $rows = [];
  while ($row = $result->fetchArray()) {
    $rows[] = $row['body'] . '(' . $row['id'] . ')';
  }
  $question = $rows[array_rand($rows)];
} else {
  $question = $row['body'] . ' (' . $row['id'] . ')';
}

// Update the `last_used` column with the current timestamp
$updateQuery = 'UPDATE prompts SET last_used = :timestamp WHERE body = :question';
$stmt = $db->prepare($updateQuery);
$stmt->bindValue(':timestamp', time(), SQLITE3_INTEGER);
$stmt->bindValue(':question', $question, SQLITE3_TEXT);
$stmt->execute();

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

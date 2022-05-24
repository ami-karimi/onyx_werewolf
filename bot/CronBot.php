<?php
ini_set('max_execution_time', -1);
ini_set( "display_errors", 1);
error_reporting( 1 );
if($_SERVER['REQUEST_METHOD'] !== "POST"){
    die('Not Allow');
}

// Load composer
require_once('jdf.php');
require "helpers.php";
require('config.php');
require __DIR__ . '/vendor/autoload.php';
use phpcron\CronBot\CronJob;
$bot_api_key  = BOT_API;
$bot_username = BOT_USERNAME;

try {
    // Create Telegram API object
  $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);
$data = json_decode(file_get_contents('php://input'), true);
$Cron = new CronJob($data);
$Cron->handler();
 $telegram->handle();

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!
    //echo $e;
    // Log telegram errors
    Longman\TelegramBot\TelegramLog::error($e);
} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    // Silence is golden!
    // Uncomment this to catch log initialisation errors
    //echo $e;
}


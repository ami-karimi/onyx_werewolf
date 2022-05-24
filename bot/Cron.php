<?php

ini_set('memory_limit', '2048M');
ini_set('display_errors', 0);
ini_set('memory_limit', -1);
ini_set('max_execution_time', 300);
error_reporting( E_ALL );
/*
 * Telegram Wop Bot Cron Job Set
 * DEV : amir Hossein Karimi At 2019-08-16 13:18
 * Version 1.0
 */

if($_SERVER['REQUEST_METHOD'] !== "POST"){
    die('Not Allow');
}
ini_set( "display_errors", 1);
$executionStartTime = microtime(true);
// Load composer
require_once('jdf.php');
require __DIR__ . '/vendor/autoload.php';



use Longman\TelegramBot\Request;
use phpcron\CronBot\cron;
require "helpers.php";
require('config.php');
$bot_api_key  = BOT_API;
$bot_username = BOT_USERNAME;
try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    /*
    Longman\TelegramBot\Request::setClient(new \GuzzleHttp\Client([

        'base_uri' => 'https://api.telegram.org',
        'proxy' => Proxy,
        'verify' => false,
        'timeout' => 2,

    ]));
    */


    $data = json_decode(file_get_contents('php://input'), true);
    $Cron = new cron($data);

    $Cron->handler();
    // Handle telegram webhook request
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
$executionEndTime = microtime(true);
$seconds = $executionEndTime - $executionStartTime;
//echo "This script took $seconds to execute.";

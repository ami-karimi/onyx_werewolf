<?php

ini_set('memory_limit', '2048M');
ini_set('display_errors', 0);
ini_set('memory_limit', -1);
ini_set('max_execution_time', 300);
ignore_user_abort(true);

error_reporting( E_ALL );

$executionStartTime = microtime(true);
// Load composer

require_once('jdf.php');
require __DIR__ . '/vendor/autoload.php';




// Error handling


use Longman\TelegramBot\Request;
use phpcron\CronBot\Hook;
use Longman\TelegramBot\Entities\Update;
require('config.php');
include 'helpers.php';
$bot_api_key  = BOT_API;
$bot_username = BOT_USERNAME;

$commands_paths = [
    __DIR__ . '/Commands/',
];


  function has_emojis_old( $string ) {


    preg_match_all( '([*#0-9](?>\\xEF\\xB8\\x8F)?\\xE2\\x83\\xA3|\\xC2[\\xA9\\xAE]|\\xE2..(\\xF0\\x9F\\x8F[\\xBB-\\xBF])?(?>\\xEF\\xB8\\x8F)?|\\xE3(?>\\x80[\\xB0\\xBD]|\\x8A[\\x97\\x99])(?>\\xEF\\xB8\\x8F)?|\\xF0\\x9F(?>[\\x80-\\x86].(?>\\xEF\\xB8\\x8F)?|\\x87.\\xF0\\x9F\\x87.|..(\\xF0\\x9F\\x8F[\\xBB-\\xBF])?|(((?<zwj>\\xE2\\x80\\x8D)\\xE2\\x9D\\xA4\\xEF\\xB8\\x8F\k<zwj>\\xF0\\x9F..(\k<zwj>\\xF0\\x9F\\x91.)?|(\\xE2\\x80\\x8D\\xF0\\x9F\\x91.){2,3}))?))', $string, $matches_emo );

    return (count( $matches_emo[0] ) === 1 ? $matches_emo[0] : false);
}

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);
    $telegram->setDownloadPath(__DIR__ . '/Download');
    if(Request::getInput()) {
        $post = json_decode(Request::getInput(), true);
        $oUpdate = new Update($post, BOT_USERNAME);
        $UpdateType = $oUpdate->getUpdateType();


        /*
        $myfile = fopen("newfile999.txt", "w") or die("Unable to open file!");
        $txt = $post;
        fwrite($myfile, $txt);
        fclose($myfile);
        */


        $CheckEmojy = false;

        if($UpdateType == "message"){
            $oMessage = $oUpdate->getMessage();
            $text = trim($oMessage->getText(true));
            $BOTAdd = false;
            if($oMessage->getNewChatMembers()) {
                $members = $oMessage->getNewChatMembers();
                if($oMessage->botAddedInChat()){
                    $BOTAdd = true;
                }
            }

            $ogp = $oMessage->getChat();
            $Type= $ogp->getType();

            $CheckEmojy = has_emojis_old($text);
            $message_type = $oMessage->getType();

            if(!$oMessage->getCommand() && !in_array($message_type, ['photo', 'document'], true) && $BOTAdd == false && strpos($text, 'joinToGAME_') !== true ) {
                if ($Type == "group" || $Type == "supergroup" ) {
                    die('Block');
                }

            }
        }


        new Hook($post,$CheckEmojy);
    }




    // Add commands paths containing your custom commands
    $telegram->addCommandsPaths($commands_paths);
    // Enable admin users
    // $telegram->enableAdmins($admin_users);
    // Enable MySQL
    //$telegram->enableMySql($mysql_credentials);
    // Logging (Error, Debug and Raw Updates)
    //Longman\TelegramBot\TelegramLog::initErrorLog(__DIR__ . "/{$bot_username}_error.log");
    //Longman\TelegramBot\TelegramLog::initDebugLog(__DIR__ . "/{$bot_username}_debug.log");
    //Longman\TelegramBot\TelegramLog::initUpdateLog(__DIR__ . "/{$bot_username}_update.log");
    // If you are using a custom Monolog instance for logging, use this instead of the above
    //Longman\TelegramBot\TelegramLog::initialize($your_external_monolog_instance);
    // Set custom Upload and Download paths

    $telegram->setUploadPath(__DIR__ . '/Upload');
    // Here you can set some command specific parameters
    // e.g. Google geocode/timezone api key for /date command
    //$telegram->setCommandConfig('date', ['google_api_key' => 'your_google_api_key_here']);
    // Botan.io integration
    //$telegram->enableBotan('your_botan_token');
    // Requests Limiter (tries to prevent reaching Telegram API limits)
   // $telegram->enableLimiter();

    /*
    Longman\TelegramBot\TelegramLog::initialize(
        new Monolog\Logger('telegram_bot', [
            (new Monolog\Handler\StreamHandler(__DIR__ . '/php-telegram-bot-debug.log', Monolog\Logger::DEBUG))->setFormatter(new Monolog\Formatter\LineFormatter(null, null, true)),
            (new Monolog\Handler\StreamHandler(__DIR__ . '/php-telegram-bot-error.log', Monolog\Logger::ERROR))->setFormatter(new Monolog\Formatter\LineFormatter(null, null, true)),
        ]),
        new Monolog\Logger('telegram_bot_updates', [
            (new Monolog\Handler\StreamHandler(__DIR__ . '/php-telegram-bot-update.log', Monolog\Logger::INFO))->setFormatter(new Monolog\Formatter\LineFormatter('%message%' . PHP_EOL)),
        ])
    );
    */


    $executionEndTime = microtime(true);
    $seconds = $executionEndTime - $executionStartTime;

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

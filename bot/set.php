<?php

require 'config.php';

require __DIR__ . '/vendor/autoload.php';

$bot_api_key  = BOT_API;
$bot_username = BOT_USERNAME;
$hook_url     = HOOK_URL;

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    // Set webhook
    $result = $telegram->setWebhook($hook_url, ['max_connections' => '999']);
    print_r($result);
    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // log telegram errors
    print_r($e);
    echo $e->getMessage();
}
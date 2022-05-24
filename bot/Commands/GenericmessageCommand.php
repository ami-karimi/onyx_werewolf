<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Longman\TelegramBot\Commands\SystemCommands;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;
use phpcron\CronBot\CM;
use phpcron\CronBot\GR;

/**
 * Generic message command
 *
 * Gets executed when any type of message is sent.
 */
class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';
    /**
     * @var string
     */
    protected $description = 'Handle generic message';
    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $UpdateType = $this->getUpdate()->getUpdateType();
        $message = $this->getMessage();
        $members = $message->getNewChatMembers();

        $from       = $message->getFrom();
        $user_id    = $from->getId();
        $chat_id = $message->getChat()->getId();
        $type_massage = $message->getChat()->getType();
        $fullNameUser = $from->getFirstName() . " " . $from->getLastName();
        $text_main = trim($message->getText());


        if($message->getChat()->getType() == "private") {
            $text = mb_substr($message->getText(),0,3);
            if($text == "پخ:"){
                CM::SendPrivateMessageCoin();
            }else{
                CM::SendPrivateMessage();
            }
        }
        if($message->getChat()->getType() == "private") {
        if($message->getText()) {
            $text = $message->getText();
            if(strpos($text,'👥 لیست گروه ها') !== false){
                CM::CM_GroupList();
            }
            if(strpos($text,'💰 خرید سکه') !== false){
                CM::CM_Coin();
            }
            if(strpos($text,'🛍  فروشگاه') !== false){
                CM::CM_Shop();
            }
            if(strpos($text,'📣 اخبار') !== false){
                CM::CM_News();
            }
            if(strpos($text,'📞 پشتیبانی') !== false){
                CM::CM_Support();
            }
            if(strpos($text,'🎓 آکادمی مافیا') !== false){
                CM::CM_Accademy();

            }
            if(strpos($text,'🩸 برترین کاربران کیل') !== false){
                CM::CM_KillList();

            }
         }
        }

        if($message->getText()){
            $text = $message->getText();
            if(CM::has_emojis_old($text)){
                    CM::EmojySend();
            }
        
        }

        $admin_users = [630127836];
        if($members){
            foreach ($members as $member){
                if($member->getIsBot()) {
                    if($member->getUsername() === "OnyxWereBetaBot") {
                        CM::BotAddToGroup();
                        GR::BotAddedToGroup();
                    }
                }
            }
        }

        $command = $message->getCommand();
        if (strpos($command, 'about') !== false) {
            $command = trim($command);
            CM::CM_Command($command);
        }


        if($forward = $message->getForwardFromChat()){

            if(in_array($user_id,$admin_users)) {
                $inline_keyboard = new InlineKeyboard(
                    [
                        ['text' => 'ارسال', 'callback_data' => 'SendMessage/'.$message->getForwardFromChat()->getId()."/".$message->getForwardFromMessageId()],['text' => 'منصرف شدم', 'callback_data' => 'cancele_forward']
                    ]
                );
                $result =  Request::sendMessage([
                    'chat_id' => $user_id,
                    'text' => " فروارد برای تمامی بازیکنان".PHP_EOL."تعداد بازیکنان :".GR::GetPlayersCount(),
                    'reply_markup' => $inline_keyboard,
                    'parse_mode' => 'HTML',
                ]);

            }

        }

       return  Request::emptyResponse();


    }
}
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
use Longman\TelegramBot\Request;
use phpcron\CronBot\CM;

class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'callbackquery';
    /**
     * @var string
     */
    protected $description = 'Reply to callback query';
    /**
     * @var string
     */
    protected $version = '1.1.1';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(){
        $string = $this->getCallbackQuery()->getData();
        $callback_id = $this->getCallbackQuery()->getId();

        if(strpos($string, 'UserLang_') !== false) {
            $ex = explode('_',$string);
             CM::GetGameMode($ex['1']);
        }elseif(strpos($string, 'UserGameMode_') !== false) {
            $ex = explode('_',$string);
            CM::ChangeGameMode($ex['1']);
        }elseif(strpos($string, 'config_') !== false) {
            $ex = explode('_',$string);
            switch ($ex['1']){
                case 'done':
                    CM::configDone();
                break;
                default:
                break;
            }
        }elseif(strpos($string, 'setting_') !== false) {
            $ex = explode('/',$string);
            $str_replace = str_replace('setting_','',$ex['0']);
            CM::GetConfigKeyboard($str_replace);
        }elseif(strpos($string, 'slectMajik') !== false) {
            $ex = explode('/',$string);
            CM::UseMajik($ex['2']);
        }elseif(strpos($string, 'todayList') !== false) {
            $ex = explode('/',$string);
            CM::GetTodayList($ex);
        }elseif(strpos($string, 'backtoconfig') !== false) {
            CM::BackToConfig();
        }elseif(strpos($string, 'configRoles_') !== false) {
            $ex = explode('/',$string);
            $str_replace = str_replace('configRoles_','',$ex['0']);
            CM::ConfigRole($str_replace);
        }elseif(strpos($string, 'configGroup_') !== false) {
            $ex = explode('/',$string);
            $str_replace = str_replace('configGroup_','',$ex['0']);
            CM::ConfigGroup($str_replace);
        }elseif(strpos($string, 'configTimer_') !== false) {
            $ex = explode('/',$string);
            $str_replace = str_replace('configTimer_','',$ex['0']);
            CM::ConfigTimer($str_replace);
        }elseif(strpos($string, 'configGame_') !== false) {
            $ex = explode('/',$string);
            $str_replace = str_replace('configGame_','',$ex['0']);
            CM::ConfigGame($str_replace);
        }elseif(strpos($string, 'configureGroup_') !== false) {
            $ex = explode('/',$string);
            $str_replace = str_replace('configureGroup_','',$ex['0']);
            CM::ChangeGroupConfig($ex['2'],$str_replace);
        }elseif(strpos($string, 'GroupLang') !== false) {
            $ex = explode('/',$string);
            CM::ChangeGroupLang($ex['2']);
        }elseif(strpos($string, 'ChangeGroupGameMode') !== false) {
            $ex = explode('/',$string);
            CM::ChangeGroupGameMode($ex['2']);
        }elseif(strpos($string, 'cancel_nextgame') !== false) {
            CM::cancel_nextgame();
        }elseif(strpos($string, 'NightSelect_') !== false) {
           $Ex = explode('/',$string);
           $str_replace = str_replace('NightSelect_','',$Ex['0']);
           CM::NightSelectedCheck($str_replace);
        }elseif(strpos($string, 'VoteSelect') !== false) {
           CM::VoteUser();
        }elseif(strpos($string, 'DdgSlVt') !== false) {
            CM::DodgeVote();
        }elseif(strpos($string, 'DySlDodge_') !== false) {
            $Ex = explode('/',$string);
            $str_replace = str_replace('DySlDodge_','',$Ex['0']);
            CM::DaySelectedDodge($str_replace);
        }elseif(strpos($string, 'RoleFireFighterFight') !== false) {
           CM::FighterFight();
        }elseif(strpos($string, 'DaySelect_') !== false) {
            $Ex = explode('/',$string);
            $str_replace = str_replace('DaySelect_','',$Ex['0']);
            CM::DaySelectedCheck($str_replace);
        }elseif(strpos($string, 'NghddgDlec_') !== false) {
            $Ex = explode('/',$string);
            $str_replace = str_replace('NghddgDlec_','',$Ex['0']);
            CM::NightSelectDodge($str_replace);
        }elseif(strpos($string, 'skip') !== false) {
            CM::Skip();
        }elseif(strpos($string, 'Kalantar_shot') !== false) {
            CM::KalanShot();
        }elseif(strpos($string, 'BanPlayer_') !== false) {
            $Ex = explode('/',$string);
            $str_replace = str_replace('BanPlayer_','',$Ex['0']);
            CM::BanPlayer($str_replace);
        }elseif(strpos($string, 'AdminSetting') !== false) {
            CM::AdminSetting();
        }elseif(strpos($string, 'locked') !== false) {
           return Request::answerCallbackQuery([
               'callback_query_id' => $callback_id,
               'text'=> "این دستور از جانب مدیر اصلی بسته شده است برای شما",
               'show_alert' => true,
               'cache_time' => 5
           ]);
        }elseif(strpos($string, 'closeBanList') !== false) {
            CM::RemoveMarkUp();
        }elseif(strpos($string, 'Grouplist_') !== false) {
            $ex = explode('_',$string);
            CM::SelectGroupList($ex['1']);
        }elseif(strpos($string, 'GroupGameMode_') !== false) {
            $ex = explode('_',$string);
            CM::SendGroupList($ex['1'],$ex['2'],(isset($ex['3']) ? true : false));
        }elseif(strpos($string, 'SendMessage') !== false) {
            $ex = explode('/',$string);
            CM::SendMessageToPV($ex['1'],$ex['2']);
        }elseif(strpos($string, 'AddFriend') !== false) {
            $ex = explode('/',$string);
            $msg_id = 0;
            if(isset($ex['2'])){
                $msg_id = $ex['2'];
            }
            CM::FriendR($ex['0'],$ex['1'],$msg_id);
        }elseif(strpos($string, 'gpgchplayer') !== false) {
            CM::ChangeGroup();
        }elseif(strpos($string, 'ReportResult') !== false) {
            $GetExplode = explode('/',$string);
            $reportId = $GetExplode[2];
            $section = $GetExplode[1];
            CM::ReportUserAdmin($reportId,$section);
        }elseif(strpos($string, 'GetCoin') !== false) {
            $GetExplode = explode('_',$string);
            CM::GetChargeItem($GetExplode[1]);
        }elseif(strpos($string, 'ShopItem') !== false) {
            $GetExplode = explode('_',$string);
            CM::ShopItemSet($GetExplode[1]);
        }elseif(strpos($string, 'BTNSP') !== false) {
            $GetExplode = explode('_',$string);
            CM::ShopCheckout($GetExplode);
        }elseif(strpos($string, 'SGFDRol') !== false) {
            $GetExplode = explode('|',$string);
            CM::ChangeRoleSetting($GetExplode);
        }elseif(strpos($string, 'setLaqabToMe') !== false) {
            $GetExplode = explode('/',$string);
            CM::SetLaqab($GetExplode[1]);
        } elseif(strpos($string, 'BetGame') !== false) {
            $GetExplode = explode('/',$string);
            CM::CreateBet($GetExplode[1]);
        } elseif(strpos($string, 'bst') !== false) {
            $GetExplode = explode('/',$string);
            CM::btsOnHou($GetExplode[1]);
        } elseif(strpos($string, 'bls_reject') !== false) {
            CM::btsReject();
        } elseif(strpos($string, 'bgs_confirm') !== false) {
            CM::btsConfirm();
        } elseif(strpos($string, 'bghChangeBet') !== false) {
            CM::ChangeBetCount();
        } elseif(strpos($string, 'upAcc') !== false) {
            $GetExplode = explode('/',$string);
            CM::upAcc($GetExplode[1]);
        } elseif(strpos($string, 'ugrade') !== false) {
            $GetExplode = explode('/',$string);
            CM::ugrade($GetExplode[1]);
        } elseif(strpos($string, 'asdopt') !== false) {
            $GetExplode = explode('/',$string);
            CM::asdopt($GetExplode[1]);
        }elseif(strpos($string, 'setGifi') !== false) {
            $GetExplode = explode('/',$string);
            CM::setGifi($GetExplode[1]);
        }elseif(strpos($string, 'delGif') !== false) {
            $GetExplode = explode('/',$string);
            CM::DelGif($GetExplode[1],$GetExplode[2]);
        }elseif(strpos($string, 'getMyGif') !== false) {
            $GetExplode = explode('/',$string);
            CM::getMyGif($GetExplode[1]);
        }elseif(strpos($string, 'settext') !== false) {
            $GetExplode = explode('/',$string);
            CM::settext($GetExplode[1]);
        }elseif(strpos($string, 'delTextPr') !== false) {
            $GetExplode = explode('/',$string);
            CM::delTextPr($GetExplode[1],$GetExplode[2]);
        }elseif(strpos($string, 'getKilllist') !== false) {
            $GetExplode = explode('/',$string);
            CM::CM_KillList($GetExplode[1],false);
        }elseif(strpos($string, 'BfdHero') !== false) {
            $GetExplode = explode('/',$string);
            CM::CreateHero($GetExplode[1]);
        }

      //  return Request::answerCallbackQuery(['callback_query_id' => $callback_id]);



    }
}

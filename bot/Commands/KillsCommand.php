<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;


use phpcron\CronBot\CM;

/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class KillsCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'NextGame Command';
    /**
     * @var string
     */
    protected $description = 'Next game command';
    /**
     * @var string
     */
    protected $usage = '/kills';
    /**
     * @var string
     */
    protected $version = '1.1.0';
    /**
     * @var bool
     */


    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        CM::CM_Kills();
    }
}
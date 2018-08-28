<?php

declare(strict_types=1);

/*
 * This file is part of the `idea` project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Messenger\TelegramChat;

use App\BotMan\Drivers\Telegram\TelegramDriver;
use App\Repository\TelegramChatRepository;
use BotMan\BotMan\BotMan;

class SendMessageToTelegramUserChatHandler
{
    /**
     * @var BotMan
     */
    private $bot;
    /**
     * @var TelegramChatRepository
     */
    private $repository;

    public function __construct(BotMan $bot, TelegramChatRepository $repository)
    {
        $this->bot = $bot;
        $this->repository = $repository;
    }

    public function __invoke(SendMessageToTelegramUserChatCommand $command): void
    {
        $chatId = $command->getChatId();
        $message = $command->getMessage();

        $telegramChat = $this->repository->find($chatId);
        if (!$telegramChat) {
            return;
        }

        $this->bot->say($message, $chatId, TelegramDriver::class);
    }
}

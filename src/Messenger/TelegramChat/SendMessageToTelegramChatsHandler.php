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
use App\Entity\TelegramChat;
use App\MessageBus\CommandHandlerInterface;
use App\Repository\TelegramChatRepository;
use BotMan\BotMan\BotMan;

class SendMessageToTelegramChatsHandler implements CommandHandlerInterface
{
    /**
     * @var TelegramChatRepository
     */
    private $repository;
    /**
     * @var BotMan
     */
    private $bot;

    public function __construct(TelegramChatRepository $repository, BotMan $bot)
    {
        $this->repository = $repository;
        $this->bot = $bot;
    }

    public function __invoke(SendMessageToTelegramChatsCommand $command): void
    {
        $message = $command->getMessage();

        $telegramChats = $this->repository->findBy(['active' => true]);

        if (empty($telegramChats)) {
            return;
        }

        $recipients = array_map(function (TelegramChat $chat) {
            return $chat->getId();
        }, $telegramChats);

        $this->bot->say($message, $recipients, TelegramDriver::class);
    }
}

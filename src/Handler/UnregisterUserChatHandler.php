<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handler;

use App\Command\UnregisterUserChatCommand;
use App\Entity\TelegramChat;
use App\Repository\TelegramChatRepository;
use App\Services\Telegram\TelegramService;

class UnregisterUserChatHandler
{
    /**
     * @var TelegramChatRepository
     */
    private $repository;
    /**
     * @var TelegramService
     */
    private $telegram;

    public function __construct(TelegramChatRepository $repository, TelegramService $telegram)
    {
        $this->repository = $repository;
        $this->telegram = $telegram;
    }

    public function handle(UnregisterUserChatCommand $command)
    {
        $message = $command->getMessage();
        $chat = $message->getChat();

        if (TelegramChat::PRIVATE !== $chat->getType()) {
            return;
        }

        $telegramChat = $this->repository->find($chat->getId());
        if ($telegramChat) {
            $this->repository->remove($telegramChat);
        }
    }
}

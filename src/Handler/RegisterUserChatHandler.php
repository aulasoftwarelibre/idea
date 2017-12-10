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

use App\Command\RegisterUserChatCommand;
use App\Entity\TelegramChat;
use App\Repository\TelegramChatRepository;
use App\Services\Telegram\TelegramService;

class RegisterUserChatHandler
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

    public function handle(RegisterUserChatCommand $command)
    {
        $message = $command->getMessage();
        $chat = $message->getChat();

        if (TelegramChat::PRIVATE !== $chat->getType()) {
            return null;
        }

        $telegramChat = $this->repository->find($chat->getId());
        if (!$telegramChat) {
            $telegramChat = new TelegramChat($chat->getId(), $chat->getType());
            $telegramChat->setUsername($chat->getUsername());

            $this->repository->add($telegramChat);
        }

        return $telegramChat;
    }
}

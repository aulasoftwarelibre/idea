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

use App\Command\LeftChatParticipantCommand;
use App\Repository\TelegramChatRepository;
use Telegram\Bot\Api;

class LeftChatParticipantHandler
{
    /**
     * @var TelegramChatRepository
     */
    private $repository;
    /**
     * @var Api
     */
    private $telegram;

    public function __construct(TelegramChatRepository $repository, Api $telegram)
    {
        $this->repository = $repository;
        $this->telegram = $telegram;
    }

    public function handle(LeftChatParticipantCommand $command)
    {
        $message = $command->getMessage();
        $chat = $message->getChat();
        $me = $this->telegram->getMe();

        if ($message->getLeftChatParticipant()->getId() !== $me->getId()) {
            return null;
        }

        $telegramChat = $this->repository->find($chat->getId());

        if ($telegramChat) {
            $this->repository->remove($telegramChat);
        }
    }
}

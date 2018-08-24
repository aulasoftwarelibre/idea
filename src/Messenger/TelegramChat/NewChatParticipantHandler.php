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

use App\Entity\TelegramChat;
use App\Repository\TelegramChatRepository;
use Telegram\Bot\Api;

class NewChatParticipantHandler
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

    public function __invoke(NewChatParticipantCommand $command): TelegramChat
    {
        $message = $command->getMessage();
        $chat = $message->getChat();
        $me = $this->telegram->getMe();

        if ($message->getNewChatParticipant()->getId() !== $me->getId()) {
            return null;
        }

        $telegramChat = $this->repository->find($chat->getId());

        if (!$telegramChat) {
            $telegramChat = new TelegramChat((string) $chat->getId(), $chat->getType());
            $telegramChat->setTitle($chat->getTitle());
            $this->repository->add($telegramChat);
        }

        return $telegramChat;
    }
}

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

class SendMessageToTelegramChatsHandler
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

    public function __invoke(SendMessageToTelegramChatsCommand $command): void
    {
        $message = $command->getMessage();
        $telegram = $this->telegram;

        $telegramChats = $this->repository->findBy(['active' => true]);

        array_map(function (TelegramChat $chat) use ($telegram, $message): void {
            $telegram->sendMessage([
                'chat_id' => (int) $chat->getId(),
                'text' => $message,
            ]);
        }, $telegramChats);
    }
}

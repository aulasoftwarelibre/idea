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

use App\Repository\TelegramChatRepository;
use Telegram\Bot\Api as Telegram;

class SendMessageToTelegramUserChatHandler
{
    /**
     * @var Telegram
     */
    private $telegram;
    /**
     * @var TelegramChatRepository
     */
    private $repository;

    public function __construct(Telegram $telegram, TelegramChatRepository $repository)
    {
        $this->telegram = $telegram;
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

        $this->telegram->sendMessage([
            'chat_id' => (int) $chatId,
            'text' => $message,
        ]);
    }
}

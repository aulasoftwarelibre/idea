<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Messenger\TelegramChat;

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

    public function __invoke(UnregisterUserChatCommand $command)
    {
        $chatId = $command->getChatId();

        $telegramChat = $this->repository->find($chatId);
        if (!$telegramChat instanceof TelegramChat || TelegramChat::PRIVATE !== $telegramChat->getType()) {
            return;
        }

        $this->repository->remove($telegramChat);
    }
}

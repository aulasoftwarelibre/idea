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

class UnregisterUserChatHandler
{
    /**
     * @var TelegramChatRepository
     */
    private $repository;

    public function __construct(TelegramChatRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UnregisterUserChatCommand $command): void
    {
        $chatId = $command->getChatId();

        $telegramChat = $this->repository->find($chatId);
        if (!$telegramChat instanceof TelegramChat || TelegramChat::PRIVATE !== $telegramChat->getType()) {
            return;
        }

        $this->repository->remove($telegramChat);
    }
}

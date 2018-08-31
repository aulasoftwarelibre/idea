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

use App\Entity\TelegramChatPrivate;
use App\Repository\TelegramChatPrivateRepository;

class DisableNotificationHandler
{
    /**
     * @var TelegramChatPrivateRepository
     */
    private $repository;

    public function __construct(TelegramChatPrivateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(DisableNotificationCommand $command): void
    {
        $chatId = $command->getMessage()->getChat()->getId();
        $notification = $command->getNotification();

        $telegramChat = $this->repository->find($chatId);
        if (!$telegramChat instanceof TelegramChatPrivate ||
             !\in_array($notification, TelegramChatPrivate::getNotificationsTypes(), true)) {
            return;
        }

        $telegramChat->removeNotification($notification);
    }
}

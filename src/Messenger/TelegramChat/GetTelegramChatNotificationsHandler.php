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
use App\Messenger\TelegramChat\Abstracts\ProcessTelegramChat;

class GetTelegramChatNotificationsHandler extends ProcessTelegramChat
{
    public function __invoke(GetTelegramChatNotificationsQuery $query): void
    {
        $chatId = $query->getChatId();

        $telegramChat = $this->repository->find($chatId);
        if (!$telegramChat instanceof TelegramChat) {
            throw new \LogicException('La cuenta no está vinculada a la plataforma de actividades.');
        }

        $this->sendMessage($query, $telegramChat);
    }
}

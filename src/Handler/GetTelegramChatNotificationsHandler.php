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

use App\Command\GetTelegramChatNotificationsQuery;
use App\Entity\TelegramChat;
use App\Handler\Abstracts\ProcessTelegramChat;

class GetTelegramChatNotificationsHandler extends ProcessTelegramChat
{
    public function handle(GetTelegramChatNotificationsQuery $query)
    {
        $chatId = $query->getChatId();

        $telegramChat = $this->repository->find($chatId);
        if (!$telegramChat instanceof TelegramChat) {
            return new \LogicException('La cuenta no está vinculada a la plataforma de actividades.');
        }

        $this->sendMessage($query, $telegramChat);
    }
}

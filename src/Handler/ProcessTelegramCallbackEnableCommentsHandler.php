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

use App\Command\ProcessTelegramCallbackEnableCommentsCommand;
use App\Entity\TelegramChat;
use App\Handler\Abstracts\ProcessTelegramChat;

class ProcessTelegramCallbackEnableCommentsHandler extends ProcessTelegramChat
{
    public function handle(ProcessTelegramCallbackEnableCommentsCommand $command)
    {
        $chatId = $command->getChatId();

        $telegramChat = $this->repository->find($chatId);
        if (!$telegramChat instanceof TelegramChat) {
            return null;
        }

        $telegramChat->addNotification(TelegramChat::NOTIFY_COMMENTS);

        $this->repository->add($telegramChat);

        $this->sendReply($command, $telegramChat);
    }
}

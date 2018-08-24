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

class ProcessTelegramCallbackEnableCommentsHandler extends ProcessTelegramChat
{
    public function __invoke(ProcessTelegramCallbackEnableCommentsCommand $command): void
    {
        $chatId = $command->getChatId();

        $telegramChat = $this->repository->find($chatId);
        if (!$telegramChat instanceof TelegramChat) {
            return;
        }

        $telegramChat->addNotification(TelegramChat::NOTIFY_COMMENTS);

        $this->repository->add($telegramChat);

        $this->sendReply($command, $telegramChat);
    }
}

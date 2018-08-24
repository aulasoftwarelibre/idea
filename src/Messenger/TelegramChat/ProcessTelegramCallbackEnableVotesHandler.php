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

class ProcessTelegramCallbackEnableVotesHandler extends ProcessTelegramChat
{
    public function __invoke(ProcessTelegramCallbackEnableVotesCommand $command): void
    {
        $chatId = (string) $command->getChatId();

        $telegramChat = $this->repository->find($chatId);
        if (!$telegramChat instanceof TelegramChat) {
            return;
        }

        $telegramChat->addNotification(TelegramChat::NOTIFY_VOTES);

        $this->repository->add($telegramChat);

        $this->sendReply($command, $telegramChat);
    }
}

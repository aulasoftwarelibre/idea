<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command\Abstracts;

abstract class ProcessTelegramChatCommand
{
    /**
     * ProcessTelegramChat constructor.
     *
     * @param int $chatId
     * @param int $messageId
     */
    public function __construct(int $chatId, int $messageId)
    {
        $this->chatId = $chatId;
        $this->messageId = $messageId;
    }

    /**
     * @return int
     */
    public function getChatId(): int
    {
        return $this->chatId;
    }

    /**
     * @return int
     */
    public function getMessageId(): int
    {
        return $this->messageId;
    }
}

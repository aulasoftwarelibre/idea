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

namespace App\Message\TelegramChat;

final class SendMessageToTelegramUserChatCommand
{
    /**
     * @var string
     */
    private $chatId;
    /**
     * @var string
     */
    private $message;

    public function __construct(string $chatId, string $message)
    {
        $this->chatId = $chatId;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getChatId(): string
    {
        return $this->chatId;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}

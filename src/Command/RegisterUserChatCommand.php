<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use Telegram\Bot\Objects\Message;

class RegisterUserChatCommand
{
    /**
     * @var Message
     */
    private $message;
    /**
     * @var null|string
     */
    private $token;

    public function __construct(Message $message, ?string $token)
    {
        $this->message = $message;
        $this->token = $token;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    /**
     * @return null|string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }
}

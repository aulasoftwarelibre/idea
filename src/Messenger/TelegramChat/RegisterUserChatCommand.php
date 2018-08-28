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

use Sgomez\Bundle\BotmanBundle\Model\Telegram\Message;

class RegisterUserChatCommand
{
    /**
     * @var Message
     */
    private $message;
    /**
     * @var string
     */
    private $token;

    public function __construct(Message $message, string $token)
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
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}

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

class LeftChatMemberCommand
{
    /**
     * @var array|string[]
     */
    private $payload;
    /**
     * @var Message
     */
    private $message;

    public function __construct(array $payload, Message $message)
    {
        $this->payload = $payload;
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }
}

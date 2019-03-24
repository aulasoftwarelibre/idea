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

use Sgomez\Bundle\BotmanBundle\Model\Telegram\Message;

final class EnableNotificationCommand
{
    /**
     * @var Message
     */
    private $message;
    /**
     * @var string
     */
    private $notification;

    public function __construct(Message $message, string $notification)
    {
        $this->message = $message;
        $this->notification = $notification;
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
    public function getNotification(): string
    {
        return $this->notification;
    }
}

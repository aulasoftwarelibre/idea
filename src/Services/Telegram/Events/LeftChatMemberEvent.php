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

namespace App\Services\Telegram\Events;

use App\Messenger\TelegramChat\LeftChatMemberCommand;
use BotMan\BotMan\BotMan;
use Sgomez\Bundle\BotmanBundle\Model\Telegram\Message;
use Symfony\Component\Messenger\MessageBusInterface;

class LeftChatMemberEvent
{
    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(array $payload, BotMan $bot): void
    {
        $message = Message::fromIncomingMessage($bot->getMessage());

        $this->bus->dispatch(
            new LeftChatMemberCommand(
                $payload,
                $message
            )
        );
    }
}

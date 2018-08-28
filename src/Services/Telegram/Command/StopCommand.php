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

namespace App\Services\Telegram\Command;

use App\Entity\TelegramChat;
use App\Messenger\TelegramChat\UnregisterUserChatCommand;
use BotMan\BotMan\BotMan;
use Sgomez\Bundle\BotmanBundle\Model\Telegram\Message;
use Symfony\Component\Messenger\MessageBusInterface;

class StopCommand
{
    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(BotMan $bot): void
    {
        $message = Message::fromIncomingMessage($bot->getMessage());

        if (TelegramChat::PRIVATE !== $message->getChat()->getType()) {
            return;
        }

        $bot->reply('Se ha desactivado el chat. Para volver a registrarte acude a la web de actividades.');

        $this->bus->dispatch(
            new UnregisterUserChatCommand(
                (string) $message->getChat()->getId()
            )
        );
    }
}

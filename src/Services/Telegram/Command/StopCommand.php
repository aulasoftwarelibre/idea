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
use App\MessageBus\CommandBus;
use App\Messenger\TelegramChat\UnregisterUserChatCommand;
use BotMan\BotMan\BotMan;
use Sgomez\Bundle\BotmanBundle\Model\Telegram\Message;

class StopCommand
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(BotMan $bot): void
    {
        $message = Message::fromIncomingMessage($bot->getMessage());

        if (TelegramChat::PRIVATE !== $message->getChat()->getType()) {
            return;
        }

        $this->commandBus->dispatch(
            new UnregisterUserChatCommand(
                (string) $message->getChat()->getId()
            )
        );

        // TODO: check really registered before removed
        $bot->reply('Se ha desactivado el chat. Para volver a registrarte acude a la web de actividades.');
    }
}

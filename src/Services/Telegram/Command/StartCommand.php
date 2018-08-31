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
use App\Entity\TelegramChatPrivate;
use App\Messenger\TelegramChat\RegisterUserChatCommand;
use BotMan\BotMan\BotMan;
use Sgomez\Bundle\BotmanBundle\Model\Telegram\Message;
use Symfony\Component\Messenger\MessageBusInterface;

class StartCommand
{
    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(BotMan $bot, string $token): void
    {
        $message = Message::fromIncomingMessage($bot->getMessage());

        if (TelegramChat::PRIVATE !== $message->getChat()->getType()) {
            return;
        }

        $valid = $this->bus->dispatch(
            new RegisterUserChatCommand(
                $message,
                $token
            )
        );

        if (!$valid instanceof TelegramChatPrivate) {
            $bot->reply('El token no es válido.');

            return;
        }

        $bot->reply(sprintf(
            'Enhorabuena te has registrado con el usuario \'%s\'. Envía /stop para desconectar',
            (string) $valid->getUser()
        ));
    }
}

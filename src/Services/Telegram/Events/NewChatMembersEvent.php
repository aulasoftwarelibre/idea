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

use App\Message\TelegramChat\NewChatMembersCommand;
use App\Message\TelegramChat\SendWelcomeMessageCommand;
use App\MessageBus\CommandBus;
use BotMan\BotMan\BotMan;
use Sgomez\Bundle\BotmanBundle\Model\Telegram\Message;

class NewChatMembersEvent
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(array $payload, BotMan $bot): void
    {
        $message = Message::fromIncomingMessage($bot->getMessage());

        $this->commandBus->dispatch(
            new NewChatMembersCommand(
                $message
            )
        );

        $this->commandBus->dispatch(
            new SendWelcomeMessageCommand(
                $message
            )
        );
    }
}

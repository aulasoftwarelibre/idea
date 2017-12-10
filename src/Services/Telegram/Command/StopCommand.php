<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Services\Telegram\Command;

use App\Command\UnregisterUserChatCommand;
use Telegram\Bot\Commands\Command;

class StopCommand extends Command
{
    protected $name = 'stop';
    protected $description = 'Unregister user';

    public function handle($arguments)
    {
        $this->replyWithMessage([
            'text' => 'Se ha desactivado el chat. Envía /start para volver a registrarlo.',
        ]);

        $message = $this->getUpdate()->getMessage();

        $this->telegram->getTacticianBus()->handle(
            new UnregisterUserChatCommand(
                $message
            )
        );
    }
}

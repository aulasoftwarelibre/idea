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

use App\Command\RegisterUserChatCommand;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected $name = 'start';
    protected $description = 'Start command to get you started';

    public function handle($arguments)
    {
        $this->replyWithMessage([
            'text' => 'Este bot aún no interacciona con usuarios. ',
        ]);

        $message = $this->getUpdate()->getMessage();

        $this->telegram->getTacticianBus()->handle(
            new RegisterUserChatCommand(
                $message
            )
        );
    }
}

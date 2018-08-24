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

use Telegram\Bot\Commands\Command;

class HelpCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'help';
    /**
     * {@inheritdoc}
     */
    protected $description = 'Muestra la ayuda.';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments): void
    {
        $commands = $this->getTelegram()->getCommands();

        $response = '';
        foreach ($commands as $name => $command) {
            $response .= sprintf('/%s - %s' . PHP_EOL, $name, $command->getDescription());
        }

        $this->replyWithMessage(['text' => $response]);
    }
}

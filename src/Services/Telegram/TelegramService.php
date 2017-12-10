<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Services\Telegram;

use League\Tactician\CommandBus;
use Telegram\Bot\Api;

final class TelegramService extends Api
{
    private $bus;

    public function setTacticianBus(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @return mixed
     */
    public function getTacticianBus()
    {
        return $this->bus;
    }
}

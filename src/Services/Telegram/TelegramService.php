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

namespace App\Services\Telegram;

use Symfony\Component\Messenger\MessageBusInterface;
use Telegram\Bot\Api;

final class TelegramService extends Api
{
    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function setMessageBus(MessageBusInterface $bus): void
    {
        $this->bus = $bus;
    }

    /**
     * @return MessageBusInterface
     */
    public function getMessageBus(): MessageBusInterface
    {
        return $this->bus;
    }
}

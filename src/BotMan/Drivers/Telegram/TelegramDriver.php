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

namespace App\BotMan\Drivers\Telegram;

use BotMan\Drivers\Telegram\TelegramDriver as BaseTelegramDriver;
use Tightenco\Collect\Support\Collection;

class TelegramDriver extends BaseTelegramDriver
{
    /**
     * @var Collection
     */
    protected $payload;

    /**
     * {@inheritdoc}
     */
    public function messagesHandled(): void
    {
        $callback = $this->payload->get('callback_query');

        if (null !== $callback) {
            $parameters = [
                'callback_query_id' => $callback['id'],
            ];

            $this->http->post($this->buildApiUrl('answerCallbackQuery'), [], $parameters);
        }
    }

    protected function isValidLoginRequest(): bool
    {
        $check_hash = $this->queryParameters->get('hash');

        if (null === $check_hash) {
            return false;
        }

        return parent::isValidLoginRequest();
    }
}

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

namespace App\Controller;

use App\BotMan\Drivers\Telegram\TelegramDriver;
use App\Services\Telegram\Command\NotifyCommand;
use App\Services\Telegram\Command\StartCommand;
use App\Services\Telegram\Command\StopCommand;
use App\Services\Telegram\Events\LeftChatMemberEvent;
use App\Services\Telegram\Events\NewChatMembersEvent;
use BotMan\BotMan\BotMan;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends AbstractController
{
    public function __invoke(BotMan $bot): Response
    {
        $bot->group(['driver' => TelegramDriver::class], function (BotMan $bot): void {
            $bot->hears('/start {token}', StartCommand::class);
            $bot->hears('/stop', StopCommand::class);
            $bot->hears('/notify', NotifyCommand::class);
            $bot->hears('^callback_notify_(enable|disable)_(.+)$', NotifyCommand::class . '@callback');

            $bot->on('new_chat_members', NewChatMembersEvent::class);
            $bot->on('left_chat_member', LeftChatMemberEvent::class);
        });

        $bot->listen();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}

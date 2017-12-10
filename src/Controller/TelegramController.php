<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Command\LeftChatParticipantCommand;
use App\Command\NewChatParticipantCommand;
use App\Services\Telegram\Command\StartCommand;
use App\Services\Telegram\Command\StopCommand;
use League\Tactician\CommandBus;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\User;

class TelegramController extends Controller
{
    /**
     * @var Api
     */
    private $telegram;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * TelegramController constructor.
     */
    public function __construct(Api $telegram, LoggerInterface $logger, CommandBus $bus)
    {
        $this->logger = $logger;
        $this->telegram = $telegram;
        $this->bus = $bus;
    }

    /**
     * @Route("/webhook", name="telegram_hook")
     */
    public function hook(Request $request)
    {
        $this->telegram->addCommand(StartCommand::class);
        $this->telegram->addCommand(StopCommand::class);
        $this->telegram->commandsHandler(true);
        $update = $this->telegram->getWebhookUpdates();

        if ($update && $update->getMessage() instanceof Message) {
            $message = $update->getMessage();
            $this->logger->debug('Message: '.\GuzzleHttp\json_encode($message->jsonSerialize()));

            if ($message->getNewChatParticipant() instanceof User) {
                $this->bus->handle(
                    new NewChatParticipantCommand(
                        $message
                    )
                );

                return new Response();
            }

            if ($message->getLeftChatParticipant() instanceof User) {
                $this->bus->handle(
                    new LeftChatParticipantCommand(
                        $message
                    )
                );

                return new Response();
            }
        }

        return new Response();
    }
}

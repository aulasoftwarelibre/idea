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

use App\Messenger\TelegramChat\Abstracts\ProcessTelegramChatCommand;
use App\Messenger\TelegramChat\LeftChatParticipantCommand;
use App\Messenger\TelegramChat\NewChatParticipantCommand;
use App\Services\Telegram\Command\HelpCommand;
use App\Services\Telegram\Command\NotifyCommand;
use App\Services\Telegram\Command\StartCommand;
use App\Services\Telegram\Command\StopCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\User;
use function GuzzleHttp\json_encode;

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
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(Api $telegram, LoggerInterface $logger, MessageBusInterface $bus)
    {
        $this->logger = $logger;
        $this->telegram = $telegram;
        $this->bus = $bus;
    }

    /**
     * @Route("/webhook", name="telegram_hook")
     */
    public function hook(Request $request): Response
    {
        $this->telegram->addCommands([
            StartCommand::class,
            StopCommand::class,
            NotifyCommand::class,
            HelpCommand::class,
        ]);
        $this->telegram->commandsHandler(true);

        $update = $this->telegram->getWebhookUpdate();

        if ($update->getCallbackQuery() instanceof CallbackQuery) {
            return $this->sendCallbackQueryResponse($update->getCallbackQuery());
        }

        if ($update->getMessage() instanceof Message) {
            $message = $update->getMessage();
            $this->logger->debug('Message: ' . json_encode($message->jsonSerialize()));

            return $this->sendMessageResponse($message);
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    private function sendCallbackQueryResponse(CallbackQuery $callback): Response
    {
        $message = $callback->getMessage();
        $chat = $message->getChat();
        $this->logger->debug((string) \json_encode($callback->jsonSerialize()));

        try {
            $this->processCallback($callback);
            $this->replyCallback($callback);
        } catch (\Throwable $e) {
            $this->telegram->sendMessage([
                'chat_id' => $chat->getId(),
                'text' => $e->getMessage(),
            ]);
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    private function processCallback(CallbackQuery $callback): void
    {
        $this->bus->dispatch(
            $this->getCallbackCommand($callback)
        );
    }

    private function getCallbackCommand(CallbackQuery $callbackQuery): ProcessTelegramChatCommand
    {
        $data = $callbackQuery->getData();
        $class = "\\App\\Command\\ProcessTelegramCallback{$data}Command";

        if (!class_exists($class)) {
            throw new \LogicException('Acción desconocida');
        }

        return new $class(
            $callbackQuery->getMessage()->getChat()->getId(),
            $callbackQuery->getMessage()->getMessageId()
        );
    }

    private function replyCallback(CallbackQuery $callback): void
    {
        $this->telegram->answerCallbackQuery([
            'callback_query_id' => $callback->getId(),
        ]);
    }

    private function sendMessageResponse(Message $message): Response
    {
        if ($message->getNewChatMember() instanceof User) {
            $this->bus->dispatch(
                new NewChatParticipantCommand(
                    $message
                )
            );
        } elseif ($message->getLeftChatMember() instanceof User) {
            $this->bus->dispatch(
                new LeftChatParticipantCommand(
                    $message
                )
            );
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}

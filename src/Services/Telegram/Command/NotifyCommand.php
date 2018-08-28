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

use App\Entity\TelegramChat;
use App\Messenger\TelegramChat\DisableNotificationCommand;
use App\Messenger\TelegramChat\EnableNotificationCommand;
use App\Repository\TelegramChatRepository;
use BotMan\BotMan\BotMan;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use Sgomez\Bundle\BotmanBundle\Model\Telegram\Message;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Translation\TranslatorInterface;

class NotifyCommand
{
    /**
     * @var MessageBusInterface
     */
    private $bus;
    /**
     * @var TelegramChatRepository
     */
    private $repository;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(MessageBusInterface $bus, TelegramChatRepository $repository, TranslatorInterface $translator)
    {
        $this->bus = $bus;
        $this->repository = $repository;
        $this->translator = $translator;
    }

    public function __invoke(BotMan $bot): void
    {
        $message = Message::fromIncomingMessage($bot->getMessage());
        $chatId = $message->getChat()->getId();

        if (TelegramChat::PRIVATE !== $message->getChat()->getType()) {
            return;
        }

        $telegramChat = $this->repository->find($chatId);
        if (!$telegramChat instanceof TelegramChat) {
            $bot->reply($this->translator->trans('account_not_connected_with_telegram'));

            return;
        }

        $keyboard = $this->createKeyboard($telegramChat);

        $bot->reply($this->translator->trans('select_notifications'), $keyboard->toArray());
    }

    public function callback(BotMan $bot, string $action, string $notification): void
    {
        $message = Message::fromIncomingMessage($bot->getMessage());
        $chatId = $message->getChat()->getId();

        if (TelegramChat::PRIVATE !== $message->getChat()->getType()) {
            return;
        }

        $telegramChat = $this->repository->find($chatId);
        if (!$telegramChat instanceof TelegramChat) {
            $bot->reply($this->translator->trans('account_not_connected_with_telegram'));

            return;
        }

        if ('enable' === $action) {
            $command = new EnableNotificationCommand($message, $notification);
        } else {
            $command = new DisableNotificationCommand($message, $notification);
        }

        $this->bus->dispatch($command);
        $keyboard = $this->createKeyboard($telegramChat);

        $parameters = [
            'chat_id' => $chatId,
            'message_id' => $message->getMessageId(),
        ];

        $bot->sendRequest('editMessageReplyMarkup', array_merge($parameters, $keyboard->toArray()));
    }

    private function createKeyboard(TelegramChat $telegramChat): Keyboard
    {
        $keyboard = Keyboard::create();
        $notifications = TelegramChat::getNotificationsTypes();

        foreach ($notifications as $notification) {
            $keyboard->addRow(
                $this->createButton($notification, \in_array($notification, $telegramChat->getNotifications(), true))
            );
        }

        return $keyboard;
    }

    private function createButton(string $value, bool $status): KeyboardButton
    {
        $response = $status ? "disable_{$value}" : "enable_{$value}";
        $data = $status ? "callback_notify_disable_{$value}" : "callback_notify_enable_{$value}";

        return KeyboardButton::create($this->translator->trans($response))->callbackData($data);
    }
}

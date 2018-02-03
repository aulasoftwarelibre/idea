<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handler\Abstracts;

use App\Command\Abstracts\ProcessTelegramChatCommand;
use App\Command\GetTelegramChatNotificationsQuery;
use App\Entity\TelegramChat;
use App\Repository\TelegramChatRepository;
use Symfony\Component\Translation\TranslatorInterface;
use Telegram\Bot\Api as Telegram;
use Telegram\Bot\Keyboard\Keyboard;

abstract class ProcessTelegramChat
{
    /**
     * @var TelegramChatRepository
     */
    protected $repository;
    /**
     * @var Telegram
     */
    protected $telegram;
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(Telegram $telegram, TelegramChatRepository $repository, TranslatorInterface $translator)
    {
        $this->repository = $repository;
        $this->telegram = $telegram;
        $this->translator = $translator;
    }

    /**
     * @param ProcessTelegramChatCommand $command
     * @param TelegramChat               $telegramChat
     *
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function sendReply(ProcessTelegramChatCommand $command, TelegramChat $telegramChat): void
    {
        $notifications = TelegramChat::getNotificationsTypes();
        $buttons = [];

        foreach ($notifications as $name => $notification) {
            $buttons[] = $this->createButton($name, in_array($notification, $telegramChat->getNotifications(), true));
        }

        $keyboard = Keyboard::make([
            'inline_keyboard' => [$buttons],
        ]);

        $this->telegram->editMessageReplyMarkup([
            'chat_id' => $command->getChatId(),
            'message_id' => $command->getMessageId(),
            'reply_markup' => $keyboard,
        ]);
    }

    /**
     * @param GetTelegramChatNotificationsQuery $command
     * @param TelegramChat                      $telegramChat
     *
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function sendMessage(GetTelegramChatNotificationsQuery $command, TelegramChat $telegramChat): void
    {
        $notifications = TelegramChat::getNotificationsTypes();
        $buttons = [];

        foreach ($notifications as $name => $notification) {
            $buttons[] = $this->createButton($name, in_array($notification, $telegramChat->getNotifications(), true));
        }

        $keyboard = Keyboard::make([
            'inline_keyboard' => [$buttons],
        ]);

        $this->telegram->sendMessage([
            'text' => 'Selecciona las notificaciones',
            'chat_id' => $command->getChatId(),
            'reply_markup' => $keyboard,
        ]);
    }

    private function createButton(string $text, bool $status)
    {
        $response = $status ? "Disable ${text}" : "Enable ${text}";

        return [
            'text' => $this->translator->trans($response),
            'callback_data' => $status ? "Disable${text}" : "Enable${text}",
        ];
    }
}

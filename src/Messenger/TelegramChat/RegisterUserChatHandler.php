<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Messenger\TelegramChat;

use App\Entity\TelegramChat;
use App\Repository\TelegramChatRepository;
use App\Repository\UserRepository;
use App\Services\Telegram\TelegramService;

class RegisterUserChatHandler
{
    /**
     * @var TelegramChatRepository
     */
    private $telegramChatRepository;
    /**
     * @var TelegramService
     */
    private $telegram;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        TelegramChatRepository $telegramChatRepository,
        UserRepository $userRepository,
        TelegramService $telegram
    ) {
        $this->telegramChatRepository = $telegramChatRepository;
        $this->telegram = $telegram;
        $this->userRepository = $userRepository;
    }

    public function __invoke(RegisterUserChatCommand $command)
    {
        $message = $command->getMessage();
        $token = $command->getToken();

        $chat = $message->getChat();
        if (TelegramChat::PRIVATE !== $chat->getType()) {
            return false;
        }

        $user = $this->userRepository->findOneByValidToken($token);
        if (!$user) {
            return false;
        }

        $telegramChat = $this->telegramChatRepository->find($chat->getId());
        if (!$telegramChat) {
            $telegramChat = new TelegramChat($chat->getId(), $chat->getType());
        }

        $telegramChat->setUsername($chat->getUsername());
        $telegramChat->setUser($user);

        $this->telegramChatRepository->add($telegramChat);

        return $telegramChat;
    }
}

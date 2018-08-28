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

namespace App\Messenger\TelegramChat;

use App\Entity\TelegramChat;
use App\Repository\TelegramChatRepository;
use App\Repository\UserRepository;

class RegisterUserChatHandler
{
    /**
     * @var TelegramChatRepository
     */
    private $telegramChatRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        TelegramChatRepository $telegramChatRepository,
        UserRepository $userRepository
    ) {
        $this->telegramChatRepository = $telegramChatRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(RegisterUserChatCommand $command): ?TelegramChat
    {
        $message = $command->getMessage();
        $token = $command->getToken();

        $user = $this->userRepository->findOneByValidToken($token);
        if (null === $user || null === $message->getFrom()) {
            return null;
        }

        $telegramChat = $this->telegramChatRepository->find($message->getFrom()->getId());
        if (!$telegramChat) {
            $telegramChat = new TelegramChat((string) $message->getChat()->getId(), $message->getChat()->getType());
        }

        $telegramChat->setUsername($message->getFrom()->getUsername() ?? null);
        $telegramChat->setUser($user);

        $this->telegramChatRepository->add($telegramChat);

        return $telegramChat;
    }
}

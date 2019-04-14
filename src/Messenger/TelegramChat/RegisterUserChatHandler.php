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
use App\Entity\TelegramChatPrivate;
use App\MessageBus\CommandHandlerInterface;
use App\Repository\TelegramChatPrivateRepository;
use App\Repository\UserRepository;

class RegisterUserChatHandler implements CommandHandlerInterface
{
    /**
     * @var TelegramChatPrivateRepository
     */
    private $telegramChatPrivateRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        TelegramChatPrivateRepository $telegramChatPrivateRepository,
        UserRepository $userRepository
    ) {
        $this->telegramChatPrivateRepository = $telegramChatPrivateRepository;
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

        $telegramChat = $this->telegramChatPrivateRepository->find($message->getFrom()->getId());
        if (!$telegramChat) {
            $telegramChat = new TelegramChatPrivate((string) $message->getChat()->getId());
        }

        $telegramChat->setUsername($message->getFrom()->getUsername() ?? null);
        $telegramChat->setUser($user);

        $this->telegramChatPrivateRepository->add($telegramChat);

        return $telegramChat;
    }
}

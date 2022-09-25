<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Entity\User;
use App\Security\User\UserManagerInterface;
use AulaSoftwareLibre\OAuth2\ClientBundle\Message\FindUserMessage;
use DateTime;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class FindUserMessageHandler implements MessageHandlerInterface
{
    public function __construct(private UserManagerInterface $userManager)
    {
    }

    public function __invoke(FindUserMessage $message): User
    {
        $username = $message->getUsername();

        $user = $this->userManager->findUserBy(['username' => $username]);
        if (! $user instanceof User) {
            $user = User::createUcoUser($username);
        }

        $user->setLastLogin(new DateTime());

        $this->userManager->updateUser($user);

        return $user;
    }
}

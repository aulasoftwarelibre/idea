<?php


namespace App\MessageHandler\User;


use App\Entity\User;
use App\Security\User\UserManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Uco\OAuth2\ClientBundle\Message\AddUserMessage;

final class AddUserCommandHandler implements MessageHandlerInterface
{
    /**
     * @var UserManagerInterface
     */
    private UserManagerInterface $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function __invoke(AddUserMessage $message): User
    {
        $username = $message->getUsername();
        $user = User::createUcoUser($username);

        $this->userManager->updateUser($user);

        return $user;
    }
}

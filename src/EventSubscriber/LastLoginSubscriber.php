<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Security\User\UserManagerInterface;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LastLoginSubscriber implements EventSubscriberInterface
{
    private UserManagerInterface $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (! ($user instanceof User)) {
            return;
        }

        $user->setLastLogin(new DateTime());
        $this->userManager->updateUser($user);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return ['security.interactive_login' => 'onSecurityInteractiveLogin'];
    }
}

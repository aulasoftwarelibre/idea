<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Router
     */
    private $router;
    /**
     * @var TokenStorage
     */
    private $tokenStorage;
    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    public function __construct(
        RouterInterface $router,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $token = $this->tokenStorage->getToken();
        if (!$token || !$token->getUser() instanceof User) {
            return;
        }

        $controller = $event->getRequest()->attributes->get('_controller');
        $user = $token->getUser();
        if (
            empty($user->getCollective())
            && 'App\Controller\ProfileController::editAction' !== $controller
            && 'App\Controller\SecurityController::logout' !== $controller
            && HttpKernel::MASTER_REQUEST === $event->getRequestType()
        ) {
            $event->setResponse(new RedirectResponse($this->router->generate('profile_edit')));
        }
    }
}

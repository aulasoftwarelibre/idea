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

namespace App\EventListener;

use App\Controller\Profile\EditProfileController;
use App\Controller\Profile\RemoveProfileController;
use App\Controller\Security\LogoutController;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        RouterInterface $router,
        TokenStorageInterface $tokenStorage
    ) {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $token = $this->tokenStorage->getToken();
        if (!$token || !$token->getUser() instanceof User) {
            return;
        }

        $controller = $event->getRequest()->attributes->get('_controller');
        /** @var User $user */
        $user = $token->getUser();
        if (
            false === $user->getHasProfile()
            && EditProfileController::class !== $controller
            && RemoveProfileController::class !== $controller
            && LogoutController::class !== $controller
            && TemplateController::class . '::templateAction' !== $controller
            && HttpKernel::MASTER_REQUEST === $event->getRequestType()
        ) {
            $event->setResponse(new RedirectResponse($this->router->generate('profile_edit')));
        }
    }
}

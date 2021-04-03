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

use App\Controller\Profile\RegisterProfileController;
use App\Controller\Profile\RemoveProfileController;
use App\Controller\Security\LogoutController;
use App\Entity\User;
use App\Message\LogPolicy\CheckUserAcceptLastPolicyVersionQuery;
use App\MessageBus\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use function assert;
use function method_exists;

class AcceptPrivacyPolicyEventSubscriber implements EventSubscriberInterface
{
    private RouterInterface $router;
    private TokenStorageInterface $tokenStorage;
    private QueryBus $queryBus;
    private SessionInterface $session;

    public function __construct(
        RouterInterface $router,
        TokenStorageInterface $tokenStorage,
        QueryBus $queryBus,
        SessionInterface $session
    ) {
        $this->router       = $router;
        $this->tokenStorage = $tokenStorage;
        $this->queryBus     = $queryBus;
        $this->session      = $session;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $token = $this->tokenStorage->getToken();
        if (! $token || ! $token->getUser() instanceof User) {
            return;
        }

        $controller = $event->getRequest()->attributes->get('_controller');
        $user       = $token->getUser();
        assert($user instanceof User);

        $userHasAccepted = $this->queryBus->query(
            new CheckUserAcceptLastPolicyVersionQuery($user)
        );

        if (
            $userHasAccepted !== false
            || $controller === RegisterProfileController::class
            || $controller === RemoveProfileController::class
            || $controller === LogoutController::class
            || TemplateController::class . '::templateAction' === $controller
            || $event->getRequestType() !== HttpKernel::MASTER_REQUEST
        ) {
            return;
        }

        $event->setResponse(new RedirectResponse($this->router->generate('profile_register')));

        if (! method_exists($this->session, 'getFlashBag')) {
            return;
        }

        $this->session->getFlashBag()->add(
            'warning',
            'Se debe aceptar la politica de privacidad'
        );
    }
}

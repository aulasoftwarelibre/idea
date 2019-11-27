<?php


namespace App\EventListener;


use App\Controller\Profile\EditProfileController;
use App\Controller\Profile\RemoveProfileController;
use App\Controller\Security\LogoutController;
use App\Entity\User;
use App\MessageBus\QueryBus;
use App\Messenger\LogPolicy\CheckUserAcceptLastPolicyVersionQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AcceptPrivacyPolicyEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var QueryBus
     */
    private $queryBus;
    /**
     * @var Session
     */
    private $session;

    public function __construct(
        RouterInterface $router,
        TokenStorageInterface $tokenStorage,
        QueryBus $queryBus,
        SessionInterface $session
    ) {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->queryBus = $queryBus;
        $this->session = $session;
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

    public function onKernelRequest(RequestEvent $event)
    {
        $token = $this->tokenStorage->getToken();
        if (!$token || !$token->getUser() instanceof User) {
            return;
        }

        $controller = $event->getRequest()->attributes->get('_controller');
        /** @var User $user */
        $user = $token->getUser();

        $userHasAccepted = $this->queryBus->query(
            new CheckUserAcceptLastPolicyVersionQuery($user)
        );

        if (
            false === $userHasAccepted
            && EditProfileController::class !== $controller
            && RemoveProfileController::class !== $controller
            && LogoutController::class !== $controller
            && HttpKernel::MASTER_REQUEST === $event->getRequestType()
        ){
            $event->setResponse(new RedirectResponse($this->router->generate('profile_edit')));
            $this->session->getFlashBag()->add(
                'warning',
                'Se debe aceptar la politica de privacidad');
        }
    }
}
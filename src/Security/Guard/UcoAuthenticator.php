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

namespace App\Security\Guard;

use App\Entity\User;
use FOS\UserBundle\Model\UserManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Uco\OAuth2\Client\Provider\UcoResourceOwner;

use function assert;
use function strtr;

class UcoAuthenticator extends SocialAuthenticator
{
    use TargetPathTrait;

    private ClientRegistry $clientRegistry;
    private UserManagerInterface $userManager;
    private RouterInterface $router;

    public function __construct(
        ClientRegistry $clientRegistry,
        UserManagerInterface $userManager,
        RouterInterface $router
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->userManager    = $userManager;
        $this->router         = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'connect_uco_check';
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getClient());
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $userResource = $this
            ->getClient()
            ->fetchUserFromToken($credentials);
        assert($userResource instanceof UcoResourceOwner);
        $userResourceId = $userResource->getId();

        $user = $this->userManager->findUserBy(['username' => $userResourceId]);
        if (! $user) {
            $user = User::createUcoUser($userResourceId);
        }

        $this->userManager->updateUser($user);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $homepagePath = $this->router->generate('homepage');
        if (! $request->getSession() instanceof Session) {
            return new RedirectResponse($homepagePath);
        }

        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);
        if (! $targetPath) {
            return new RedirectResponse($homepagePath);
        }

        return new RedirectResponse($targetPath);
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, ?AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            $this->router->generate('connect_uco_start'),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    private function getClient(): OAuth2ClientInterface
    {
        return $this
            ->clientRegistry
            ->getClient('uco');
    }
}

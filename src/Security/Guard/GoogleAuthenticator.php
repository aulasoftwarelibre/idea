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
use App\Security\User\UserManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use function strtr;

class GoogleAuthenticator extends SocialAuthenticator
{
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
        return $request->attributes->get('_route') === 'connect_google_check';
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
        $userResource   = $this
            ->getClient()
            ->fetchUserFromToken($credentials);
        $userResourceId = $userResource->getId() . '@google.com';

        $user = $this->userManager->findUserBy(['username' => $userResourceId]);
        if (! $user) {
            $user = User::createExternalUser($userResourceId);
        }

        $email = $userResource->toArray()['email'];
        $user->setEmail($email);
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
        $targetPath = $this->getTargetPath($request, $providerKey);

        if (! $targetPath) {
            // Change it to your default target
            $targetPath = $this->router->generate('homepage');
        }

        return new RedirectResponse($targetPath);
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, ?AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            $this->router->generate('connect_google_start'),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    private function getClient(): OAuth2ClientInterface
    {
        return $this
            ->clientRegistry
            ->getClient('google');
    }

    /**
     * Returns the URL (if any) the user visited that forced them to login.
     */
    protected function getTargetPath(Request $request, string $providerKey): ?string
    {
        if (! $request->hasSession()) {
            return null;
        }

        return $request->getSession()->get('_security.' . $providerKey . '.target_path');
    }
}

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
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GithubAuthenticator extends SocialAuthenticator
{
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;
    /**
     * @var UserManagerInterface
     */
    private $userManager;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        ClientRegistry $clientRegistry,
        UserManagerInterface $userManager,
        RouterInterface $router
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->userManager = $userManager;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        return 'connect_github_check' === $request->attributes->get('_route');
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
        /** @var GithubResourceOwner $userResource */
        $userResource = $this
            ->getClient()
            ->fetchUserFromToken($credentials);
        $userResourceId = $userResource->getId() . '@github.com';

        $user = $this->userManager->findUserBy(['username' => $userResourceId]);
        if (!$user) {
            $user = User::createExternalUser($userResourceId);
        }

        $email = $this->getEmailFromGithub($credentials);
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

        if (!$targetPath) {
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
            $this->router->generate('connect_github_start'),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    private function getClient(): OAuth2ClientInterface
    {
        return $this
            ->clientRegistry
            ->getClient('github');
    }

    /**
     * Returns the URL (if any) the user visited that forced them to login.
     */
    protected function getTargetPath(Request $request, string $providerKey): ?string
    {
        if (!$request->hasSession()) {
            return null;
        }

        return $request->getSession()->get('_security.' . $providerKey . '.target_path');
    }

    /**
     * @param AccessTokenInterface|string $credentials
     *
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    private function getEmailFromGithub($credentials): string
    {
        $provider = $this->getClient()->getOAuth2Provider();
        $request = $provider->getAuthenticatedRequest(
            'GET',
            'https://api.github.com/user/emails',
            $credentials
        );
        $response = $provider->getParsedResponse($request);

        return array_reduce($response, static function ($current, $email) {
            if ($email['primary']) {
                return $email['email'];
            }

            return $current;
        }, '');
    }
}

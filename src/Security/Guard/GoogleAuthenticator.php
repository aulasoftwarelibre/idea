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
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

use function assert;
use function strtr;

class GoogleAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    use TargetPathTrait;

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly UserManagerInterface $userManager,
        private readonly RouterInterface $router,
    ) {
    }

    public function start(Request $request, AuthenticationException|null $authException = null): RedirectResponse
    {
        return new RedirectResponse(
            $this->router->generate('connect_google_start'),
            Response::HTTP_TEMPORARY_REDIRECT,
        );
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        assert($client instanceof GoogleClient);
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                $userFromToken = $client->fetchUserFromToken($accessToken);
                assert($userFromToken instanceof GoogleUser);
                $userResourceId = $userFromToken->getId() . '@google.com';

                $user = $this->userManager->findUserBy(['username' => $userResourceId]);
                if (! $user) {
                    $user = User::createExternalUser($userResourceId);
                }

                $email = $userFromToken->getEmail();
                $user->setEmail($email);
                $this->userManager->updateUser($user);

                return $user;
            }),
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response|null
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): Response|null
    {
        $homepagePath = $this->router->generate('homepage');
        if (! $request->getSession() instanceof Session) {
            return new RedirectResponse($homepagePath);
        }

        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);
        if (! $targetPath) {
            return new RedirectResponse($homepagePath);
        }

        return new RedirectResponse($targetPath);
    }
}

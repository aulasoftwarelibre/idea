<?php

declare(strict_types=1);

/*
 * This file is part of the `scav` project.
 *
 * (c) Servicio de Informática de la Universidad de Córdoba
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security\Guard;

use App\Entity\User;
use FOS\UserBundle\Model\UserManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Uco\OAuth2\Client\Provider\UcoResourceOwner;

class UcoAuthenticator extends SocialAuthenticator
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
        return 'connect_uco_check' === $request->attributes->get('_route');
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
        /** @var UcoResourceOwner $userResource */
        $userResource = $this
            ->getClient()
            ->fetchUserFromToken($credentials);
        $userResourceId = $userResource->getId();

        $user = $this->userManager->findUserBy(['username' => $userResourceId]);
        if (!$user) {
            /** @var User $user */
            $user = $this->userManager->createUser();

            $user
                ->setUsername($userResourceId)
                ->setSspId($userResourceId)
                ->setEmail("${userResourceId}@uco.es")
                ->setPassword('!')
                ->setEnabled(true);
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
            $this->router->generate('connect_uco_start'),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    private function getClient(): OAuth2Client
    {
        return $this
            ->clientRegistry
            ->getClient('uco');
    }

    /**
     * Returns the URL (if any) the user visited that forced them to login.
     *
     * @param Request $request
     * @param string  $providerKey
     *
     * @return string|null
     */
    protected function getTargetPath(Request $request, $providerKey): ?string
    {
        if (null === $request->getSession()) {
            return null;
        }

        return $request->getSession()->get('_security.' . $providerKey . '.target_path');
    }
}

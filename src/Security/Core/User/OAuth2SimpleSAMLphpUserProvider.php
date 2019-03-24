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

namespace App\Security\Core\User;

use App\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;

class OAuth2SimpleSAMLphpUserProvider extends FOSUBUserProvider
{
    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        if (empty($username)) {
            throw new AccountNotLinkedException(sprintf('Username is empty.'));
        }

        $email = $response->getEmail();
        if (empty($email)) {
            throw new AccountNotLinkedException(sprintf('Email is empty.'));
        }

        $user = $this->userManager->findUserBy([$this->getProperty($response) => $username]);
        if ($user) {
            return $user;
        }

        /** @var User $user */
        $user = $this->userManager->createUser();
        $user
            ->setUsername($username)
            ->setSspId($username)
            ->setSspAccessToken($response->getAccessToken())
            ->setEmail($email)
            ->setPassword('!')
            ->setEnabled(true);

        $this->userManager->updateUser($user);

        return $user;
    }
}

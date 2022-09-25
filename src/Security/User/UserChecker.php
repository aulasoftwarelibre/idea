<?php

declare(strict_types=1);

namespace App\Security\User;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    /** @inheritDoc */
    public function checkPreAuth(UserInterface $user)
    {
        if (! $user instanceof User) {
            return;
        }
    }

    /** @inheritDoc */
    public function checkPostAuth(UserInterface $user)
    {
        if (! $user instanceof User) {
            return;
        }

        if (! $user->getEnabled()) {
            throw new AccountExpiredException('Cuenta desactivada');
        }
    }
}

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

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

use function assert;

class AddIdeaVoter extends Voter
{
    public const ADD = 'IDEA_ADD';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::ADD;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        assert($user instanceof User);
        // if the user is anonymous, do not grant access
        if (! $user instanceof UserInterface) {
            return false;
        }

        return $user->isExternal() === false;
    }
}

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

use App\Entity\Idea;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function assert;

class EditIdeaVoter extends Voter
{
    public const EDIT = 'EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute !== self::EDIT) {
            return false;
        }

        return $subject instanceof Idea;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        assert($subject instanceof Idea);

        if (! $user instanceof User) {
            return false;
        }

        return $user->equalsTo($subject->getOwner())
            || $user->hasRole('ROLE_ADMIN');
    }
}

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

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        if ($attribute !== self::EDIT) {
            return false;
        }

        return $subject instanceof Idea;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        assert($user instanceof User);

        if (! $user instanceof User) {
            return false;
        }

        return $user->equalsTo($subject->getOwner())
            || $user->hasRole('ROLE_ADMIN');
    }
}

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
use App\Entity\Vote;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ShowIdeaVoter extends Voter
{
    /**
     * @var string
     */
    public const SHOW = 'SHOW';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        if (self::SHOW !== $attribute) {
            return false;
        }

        if ($subject instanceof Idea) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $user->hasRole('ROLE_ADMIN')
            || $subject->getVotes()->exists(function (Vote $vote) use ($user) {
                return $vote->getUser()->equalsTo($user);
            });
    }
}

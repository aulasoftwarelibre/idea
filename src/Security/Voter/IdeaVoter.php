<?php

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

class IdeaVoter extends Voter
{
    const OWNER = 'OWNER';
    const EDIT = 'EDIT';
    const SHOW = 'SHOW';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [static::OWNER, static::EDIT, static::SHOW], true)) {
            return false;
        }

        if ($subject instanceof Idea) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Idea $idea */
        $idea = $subject;

        switch ($attribute) {
            case static::OWNER:
                return $this->isOwner($user, $idea);
            case static::EDIT:
                return $this->canEdit($user, $idea);
            case static::SHOW:
                return $this->canShow($user, $idea);
        }

        throw new \RuntimeException("This line shouldn't be reached");
    }

    private function isOwner(User $user, Idea $idea)
    {
        return $user->equalsTo($idea->getOwner());
    }

    private function canEdit(User $user, Idea $idea)
    {
        return $user->equalsTo($idea->getOwner())
            || $user->hasRole('ROLE_ADMIN')
            ;
    }

    private function canShow(User $user, Idea $idea)
    {
        return $user->hasRole('ROLE_ADMIN')
            || $idea->getVotes()->exists(function (Vote $vote) use ($user) {
                return $vote->getUser()->equalsTo($user);
            });
    }
}

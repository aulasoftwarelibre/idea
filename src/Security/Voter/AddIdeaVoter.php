<?php

namespace App\Security\Voter;

use App\Entity\Idea;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AddIdeaVoter extends Voter
{
    public const ADD = 'IDEA_ADD';

    /**
     * {@inheritDoc}
     */
    protected function supports($attribute, $subject)
    {
        return $attribute === self::ADD;
    }

    /**
     * {@inheritDoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        return $user->isExternal() === false;
    }
}

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
use Symfony\Component\Security\Core\User\UserInterface;

class JoinIdeaVoter extends Voter
{
    public const JOIN = 'IDEA_JOIN';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return self::JOIN === $attribute
            && $subject instanceof Idea;
    }

    /**
     * {@inheritdoc}
     *
     * @param Idea $subject
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($subject->isClosed()) {
            return false;
        }

        return $this->checkUserCanJoin($user, $subject);
    }

    private function checkUserCanJoin(User $user, Idea $subject): bool
    {
        if (false === $this->checkFreeSeats($subject)) {
            return false;
        }

        if (false === $this->checkOpenForExternal($user, $subject)) {
            return false;
        }

        return true;
    }

    private function checkFreeSeats(Idea $idea): bool
    {
        if (0 === $idea->getNumSeats()) {
            return true;
        }

        if ($idea->getVotes()->count() >= $idea->getNumSeats()) {
            return false;
        }

        return true;
    }

    private function checkOpenForExternal(User $user, Idea $idea): bool
    {
        if ($user->isExternal() && Idea::STATE_APPROVED !== $idea->getState()) {
            return false;
        }

        if ($user->isExternal() && $idea->getExternalVotes()->count() >= $idea->getExternalNumSeats()) {
            return false;
        }

        return true;
    }
}

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

use function assert;

class JoinIdeaVoter extends Voter
{
    public const JOIN = 'IDEA_JOIN';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::JOIN
            && $subject instanceof Idea;
    }

    /**
     * {@inheritdoc}
     *
     * @param Idea $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        assert($user instanceof User);

        if (! $user instanceof UserInterface) {
            return false;
        }

        if ($subject->isClosed()) {
            return false;
        }

        return $this->checkUserCanJoin($user, $subject);
    }

    private function checkUserCanJoin(User $user, Idea $subject): bool
    {
        if (! $this->checkFreeSeats($subject)) {
            return false;
        }

        return ! $user->isExternal() || $this->checkFreeSeatsForExternal($subject);
    }

    private function checkFreeSeats(Idea $idea): bool
    {
        return $idea->countFreeSeats() > 0;
    }

    private function checkFreeSeatsForExternal(Idea $idea): bool
    {
        if ($idea->isInternal()) {
            return false;
        }

        return $idea->countFreeExternalSeats() > 0;
    }
}

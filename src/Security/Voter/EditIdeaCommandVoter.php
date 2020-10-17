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
use App\Message\Idea\CloseIdeaCommand;
use App\Message\Idea\UpdateIdeaCommand;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function assert;

class EditIdeaCommandVoter extends Voter
{
    public const HANDLE = 'handle';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        if ($attribute !== self::HANDLE) {
            return false;
        }

        return $subject instanceof CloseIdeaCommand || $subject instanceof UpdateIdeaCommand;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (! $user instanceof User) {
            return false;
        }

        $idea = $subject->getIdea();
        assert($idea instanceof Idea);

        return $user->equalsTo($idea->getOwner())
            || $user->hasRole('ROLE_ADMIN');
    }
}

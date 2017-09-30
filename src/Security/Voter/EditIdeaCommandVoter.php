<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security\Voter;

use App\Command\CloseIdeaCommand;
use App\Command\UpdateIdeaCommand;
use App\Entity\Idea;
use App\Entity\User;
use App\Repository\IdeaRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EditIdeaCommandVoter extends Voter
{
    const HANDLE = 'handle';
    /**
     * @var IdeaRepository
     */
    private $repository;

    /**
     * CloseIdeaVoter constructor.
     */
    public function __construct(IdeaRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function supports($attribute, $subject)
    {
        if (static::HANDLE !== $attribute) {
            return false;
        }

        if ($subject instanceof CloseIdeaCommand) {
            return true;
        }

        if ($subject instanceof UpdateIdeaCommand) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Idea $idea */
        $idea = $subject->getIdea();

        return $user->equalsTo($idea->getOwner())
            || $user->hasRole('ROLE_ADMIN')
            ;
    }
}

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
use App\Entity\Idea;
use App\Entity\User;
use App\Repository\IdeaRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CloseIdeaVoter extends Voter
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
        if (!$subject instanceof CloseIdeaCommand) {
            return false;
        }

        return in_array($attribute, [
            static::HANDLE,
        ], true);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Idea $idea */
        $idea = $subject->getIdea();

        switch ($attribute) {
            case self::HANDLE:
                return $this->canHandle($idea, $user);
        }

        throw new \LogicException('This code should not be reached');
    }

    private function canHandle(Idea $idea, User $user)
    {
        return
            $idea->getOwner()->getId() === $user->getId()
            || in_array('ROLE_ADMIN', $user->getRoles(), true)
        ;
    }
}

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
use App\Repository\VoteRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class MemberIdeaVoter extends Voter
{
    public const MEMBER = 'IDEA_MEMBER';
    private VoteRepository $voteRepository;

    public function __construct(VoteRepository $voteRepository)
    {
        $this->voteRepository = $voteRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $attribute === self::MEMBER
            && $subject instanceof Idea;
    }

    /**
     * {@inheritdoc}
     *
     * @param Idea $subject
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (! $user instanceof UserInterface) {
            return false;
        }

        $vote = $this->voteRepository->findOneBy(['idea' => $subject, 'user' => $token->getUser()]);

        return (bool) $vote;
    }
}

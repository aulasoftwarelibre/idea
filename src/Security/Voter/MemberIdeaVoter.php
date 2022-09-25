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

    public function __construct(private VoteRepository $voteRepository)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
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
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (! $user instanceof UserInterface) {
            return false;
        }

        $vote = $this->voteRepository->findOneBy(['idea' => $subject, 'user' => $token->getUser()]);

        return (bool) $vote;
    }
}

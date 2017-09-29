<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Twig\Extension;

use App\Entity\Idea;
use App\Entity\Thread;
use App\Entity\User;
use App\Repository\ThreadRepository;
use App\Repository\VoteRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CeoExtension extends \Twig_Extension
{
    /**
     * @var ThreadRepository
     */
    private $threadRepository;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var VoteRepository
     */
    private $voteRepository;

    public function __construct(
        VoteRepository $voteRepository,
        ThreadRepository $threadRepository,
        TranslatorInterface $translator,
        TokenStorageInterface $tokenStorage
    ) {
        $this->threadRepository = $threadRepository;
        $this->translator = $translator;
        $this->tokenStorage = $tokenStorage;
        $this->voteRepository = $voteRepository;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('idea_count_comments', [$this, 'getIdeaCountComments']),
            new \Twig_SimpleFunction('is_voted', [$this, 'testLoggedUserVotes']),
        ];
    }

    public function testLoggedUserVotes(Idea $idea)
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $vote = $this->voteRepository->findOneBy(['idea' => $idea, 'user' => $user]);

        return (bool) $vote;
    }

    public function getIdeaCountComments(Idea $idea)
    {
        $ideaId = $idea->getId();
        $thread = $this->threadRepository->find($ideaId);

        if (!$thread instanceof Thread) {
            $count = 0;
        } else {
            $count = $thread->getNumComments();
        }

        return $this->translator->transChoice('idea_num_comments', $count, ['%count%' => $count]);
    }
}

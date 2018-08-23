<?php

/*
 * This file is part of the `idea` project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Messenger\Vote;

use App\Entity\Vote;
use App\Exception\NoMoreSeatsLeftException;
use App\Repository\IdeaRepository;
use App\Repository\VoteRepository;

class AddVoteHandler
{
    /**
     * @var IdeaRepository
     */
    private $repository;

    public function __construct(VoteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(AddVoteCommand $command)
    {
        $idea = $command->getIdea();
        $user = $command->getUser();

        /** @var Vote $vote */
        $vote = $this->repository->findOneBy([
            'user' => $user,
            'idea' => $idea,
        ]);

        if ($vote instanceof Vote) {
            return;
        }

        $count = $idea->getVotes()->count();
        $numSeats = $idea->getNumSeats();

        if ($numSeats > 0 && $count >= $numSeats) {
            throw new NoMoreSeatsLeftException();
        }

        $vote = new Vote();
        $vote->setIdea($idea);
        $vote->setUser($user);

        $this->repository->add($vote);
    }
}

<?php
/**
 * This file is part of the ceo.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 * (c) Sergio Gómez <sergio@uco.es>
 * (c) Omar Sotillo <i32sofro@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handler;

use App\Command\AddVoteCommand;
use App\Entity\Vote;
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

    public function handle(AddVoteCommand $command)
    {
        $idea = $command->getIdea();
        $user = $command->getUser();

        $vote = $this->repository->findOneBy([
            'user' => $user,
            'idea' => $idea,
        ]);

        if ($vote instanceof Vote) {
            return;
        }

        $vote = new Vote();
        $vote->setIdea($idea);
        $vote->setUser($user);

        $this->repository->add($vote);
    }
}

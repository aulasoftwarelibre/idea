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

namespace App\MessageHandler\Vote;

use App\Entity\Vote;
use App\Message\Vote\RemoveVoteCommand;
use App\MessageBus\CommandHandlerInterface;
use App\Repository\VoteRepository;

final class RemoveVoteHandler implements CommandHandlerInterface
{
    /**
     * @var VoteRepository
     */
    private $repository;

    /**
     * RemoveVoteHandler constructor.
     */
    public function __construct(VoteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(RemoveVoteCommand $command): void
    {
        $idea = $command->getIdea();
        $owner = $idea->getOwner();
        $user = $command->getUser();

        $vote = $this->repository->findOneBy([
            'user' => $user,
            'idea' => $idea,
        ]);

        if ($vote instanceof Vote && false === $owner->equalsTo($user)) {
            $this->repository->remove($vote);
        }
    }
}

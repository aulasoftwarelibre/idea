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

namespace App\Messenger\Idea;

use App\Entity\Idea;
use App\Entity\Vote;
use App\MessageBus\CommandHandlerInterface;
use App\Repository\IdeaRepository;

class AddIdeaHandler implements CommandHandlerInterface
{
    /**
     * @var IdeaRepository
     */
    private $repository;

    /**
     * AddIdeaHandler constructor.
     */
    public function __construct(IdeaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(AddIdeaCommand $command): Idea
    {
        $title = $command->getTitle();
        $description = $command->getDescription();
        $user = $command->getUser();
        $group = $command->getGroup();

        $vote = new Vote();
        $vote->setUser($user);

        $idea = new Idea($title, $description, $user, $group);
        $idea->addVote($vote);

        $this->repository->add($idea);

        return $idea;
    }
}

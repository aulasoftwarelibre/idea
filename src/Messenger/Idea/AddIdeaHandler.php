<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Messenger\Idea;

use App\Entity\Idea;
use App\Entity\Vote;
use App\Repository\IdeaRepository;

class AddIdeaHandler
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

    public function __invoke(AddIdeaCommand $command)
    {
        $title = $command->getTitle();
        $description = $command->getDescription();
        $user = $command->getUser();
        $group = $command->getGroup();

        $vote = new Vote();
        $vote->setUser($user);

        $idea = new Idea();
        $idea
            ->setTitle($title)
            ->setDescription($description)
            ->setOwner($user)
            ->setGroup($group)
            ->addVote($vote)
        ;

        $this->repository->add($idea);

        return $idea;
    }
}

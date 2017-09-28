<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handler;

use App\Command\RejectIdeaCommand;
use App\Repository\IdeaRepository;

class RejectIdeaHandler
{
    /**
     * @var IdeaRepository
     */
    private $repository;

    /**
     * RejectIdeaHandler constructor.
     *
     * @param IdeaRepository $repository
     */
    public function __construct(IdeaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(RejectIdeaCommand $command)
    {
        $idea = $command->getIdea();
        $idea->setApproved(false);

        $this->repository->add($idea);
    }
}

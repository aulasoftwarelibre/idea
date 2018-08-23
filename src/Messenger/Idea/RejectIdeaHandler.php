<?php

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

    public function __invoke(RejectIdeaCommand $command)
    {
        $idea = $command->getIdea();

        if ($idea->isClosed()) {
            return;
        }

        $idea->setState(Idea::STATE_REJECTED);

        $this->repository->add($idea);
    }
}

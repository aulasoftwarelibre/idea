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
use App\Repository\IdeaRepository;

class ApproveIdeaHandler
{
    /**
     * @var IdeaRepository
     */
    private $repository;

    /**
     * ApproveIdeaHandler constructor.
     *
     * @param IdeaRepository $repository
     */
    public function __construct(IdeaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(ApproveIdeaCommand $command)
    {
        $idea = $command->getIdea();

        if ($idea->isClosed()) {
            return;
        }

        $idea->setState(Idea::STATE_APPROVED);

        $this->repository->add($idea);
    }
}

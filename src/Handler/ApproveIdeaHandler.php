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

use App\Command\ApproveIdeaCommand;
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

    public function handle(ApproveIdeaCommand $command)
    {
        $idea = $command->getIdea();

        // TODO: Approve idea

        $this->repository->add($idea);
    }
}

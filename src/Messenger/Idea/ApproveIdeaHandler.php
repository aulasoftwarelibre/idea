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
use App\Repository\IdeaRepository;

class ApproveIdeaHandler
{
    /**
     * @var IdeaRepository
     */
    private $repository;

    /**
     * ApproveIdeaHandler constructor.
     */
    public function __construct(IdeaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(ApproveIdeaCommand $command): void
    {
        $idea = $command->getIdea();

        if ($idea->isClosed()) {
            return;
        }

        $idea->setState(Idea::STATE_APPROVED);

        $this->repository->add($idea);
    }
}

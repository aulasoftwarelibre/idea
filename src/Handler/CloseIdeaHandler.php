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

use App\Command\CloseIdeaCommand;
use App\Repository\IdeaRepository;

class CloseIdeaHandler
{
    /**
     * @var IdeaRepository
     */
    private $repository;

    /**
     * CloseIdeaHandler constructor.
     *
     * @param IdeaRepository $repository
     */
    public function __construct(IdeaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(CloseIdeaCommand $command)
    {
        $idea = $command->getIdea();
        $idea->setClosed(true);

        $this->repository->add($idea);
    }
}

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

namespace App\MessageHandler\Idea;

use App\Message\Idea\CloseIdeaCommand;
use App\Repository\IdeaRepository;

class CloseIdeaCommandHandler
{
    public function __construct(private IdeaRepository $ideaRepository)
    {
    }

    public function __invoke(CloseIdeaCommand $command): void
    {
        $idea = $command->getIdea();
        $idea->setClosed(true);

        $this->ideaRepository->add($idea);
    }
}

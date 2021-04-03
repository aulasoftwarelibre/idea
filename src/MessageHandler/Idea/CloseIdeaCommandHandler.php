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

use App\Entity\Thread;
use App\Message\Idea\CloseIdeaCommand;
use App\Repository\IdeaRepository;
use App\Repository\ThreadRepository;

use function assert;

class CloseIdeaCommandHandler
{
    private IdeaRepository $ideaRepository;
    private ThreadRepository $threadRepository;

    public function __construct(IdeaRepository $ideaRepository, ThreadRepository $threadRepository)
    {
        $this->ideaRepository   = $ideaRepository;
        $this->threadRepository = $threadRepository;
    }

    public function __invoke(CloseIdeaCommand $command): void
    {
        $idea = $command->getIdea();
        $idea->setClosed(true);

        $this->ideaRepository->add($idea);

        $thread = $this->threadRepository->find($idea->getId());
        assert($thread instanceof Thread);
        $thread->setCommentable(false);

        $this->threadRepository->add($thread);
    }
}

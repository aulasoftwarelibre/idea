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

use App\Entity\Thread;
use App\MessageBus\CommandHandlerInterface;
use App\Repository\IdeaRepository;
use App\Repository\ThreadRepository;

class CloseIdeaHandler implements CommandHandlerInterface
{
    /**
     * @var IdeaRepository
     */
    private $ideaRepository;
    /**
     * @var ThreadRepository
     */
    private $threadRepository;

    /**
     * CloseIdeaHandler constructor.
     */
    public function __construct(IdeaRepository $ideaRepository, ThreadRepository $threadRepository)
    {
        $this->ideaRepository = $ideaRepository;
        $this->threadRepository = $threadRepository;
    }

    public function __invoke(CloseIdeaCommand $command): void
    {
        $idea = $command->getIdea();
        $isClosed = $command->isClosed();
        $idea->setClosed($isClosed);

        $this->ideaRepository->add($idea);

        /** @var Thread $thread */
        $thread = $this->threadRepository->find($idea->getId());
        $thread->setCommentable(!$isClosed);

        $this->threadRepository->add($thread);
    }
}

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
use App\MessageBus\CommandHandlerInterface;
use App\Repository\IdeaRepository;

class UpdateIdeaHandler implements CommandHandlerInterface
{
    /**
     * @var IdeaRepository
     */
    private $repository;

    public function __construct(IdeaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UpdateIdeaCommand $command): Idea
    {
        $idea = $command->getIdea();
        $title = $command->getTitle();
        $description = $command->getDescription();
        $group = $command->getGroup();

        $idea
            ->setTitle($title)
            ->setDescription($description)
            ->setGroup($group);

        $this->repository->add($idea);

        return $idea;
    }
}

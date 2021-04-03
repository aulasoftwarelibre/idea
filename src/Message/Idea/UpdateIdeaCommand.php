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

namespace App\Message\Idea;

use App\Entity\Group;
use App\Entity\Idea;

final class UpdateIdeaCommand
{
    private Idea $idea;
    private string $title;
    private string $description;
    private Group $group;

    public function __construct(
        Idea $idea,
        string $title,
        string $description,
        Group $group
    ) {
        $this->idea        = $idea;
        $this->title       = $title;
        $this->description = $description;
        $this->group       = $group;
    }

    public function getIdea(): Idea
    {
        return $this->idea;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }
}

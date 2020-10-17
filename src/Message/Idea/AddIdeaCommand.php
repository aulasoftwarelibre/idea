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
use App\Entity\User;

final class AddIdeaCommand
{
    private string $title;
    private string $description;
    private User $user;
    private Group $group;

    public function __construct(
        string $title,
        string $description,
        User $user,
        Group $group
    ) {
        $this->title       = $title;
        $this->description = $description;
        $this->user        = $user;
        $this->group       = $group;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }
}

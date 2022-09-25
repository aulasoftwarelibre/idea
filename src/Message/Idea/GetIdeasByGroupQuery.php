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

final class GetIdeasByGroupQuery
{
    public function __construct(private int $page, private Group $group)
    {
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    public function getGroup(): mixed
    {
        return $this->group;
    }

    public function setGroup(mixed $group): void
    {
        $this->group = $group;
    }
}

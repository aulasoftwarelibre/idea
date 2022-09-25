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

final class GetIdeasByPageQuery
{
    public function __construct(private int $page, private bool $showPrivates, private string|null $pattern)
    {
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getShowPrivates(): bool
    {
        return $this->showPrivates;
    }

    public function getPattern(): string|null
    {
        return $this->pattern;
    }
}

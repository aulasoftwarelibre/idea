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
    private int $page;
    private bool $showPrivates;
    private ?string $pattern;

    public function __construct(int $page, bool $showPrivates, ?string $pattern)
    {
        $this->page         = $page;
        $this->showPrivates = $showPrivates;
        $this->pattern      = $pattern;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getShowPrivates(): bool
    {
        return $this->showPrivates;
    }

    public function getPattern(): ?string
    {
        return $this->pattern;
    }
}

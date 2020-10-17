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
    /**
     * @var int
     */
    private $page;
    /**
     * @var bool
     */
    private $showPrivates;

    /**
     * GetIdeaPageQuery constructor.
     */
    public function __construct(int $page = 1, bool $showPrivates = false)
    {
        $this->page = $page;
        $this->showPrivates = $showPrivates;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return bool
     */
    public function getShowPrivates(): bool
    {
        return $this->showPrivates;
    }
}

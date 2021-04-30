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

use App\Message\Idea\GetIdeasByPageQuery;
use App\Repository\IdeaRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class GetIdeasByPageQueryHandler
{
    private IdeaRepository $repository;

    public function __construct(IdeaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(GetIdeasByPageQuery $query): Paginator
    {
        $page         = $query->getPage();
        $showPrivates = $query->getShowPrivates();
        $pattern      = $query->getPattern();

        return $this->repository->findLatest($page, $showPrivates, $pattern);
    }
}

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

use App\Message\Idea\GetIdeasByGroupQuery;
use App\Repository\IdeaRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class GetIdeasByGroupQueryHandler
{
    public function __construct(private IdeaRepository $repository)
    {
    }

    public function __invoke(GetIdeasByGroupQuery $query): Paginator
    {
        $page  = $query->getPage();
        $group = $query->getGroup();

        return $this->repository->findByGroup($group, $page);
    }
}

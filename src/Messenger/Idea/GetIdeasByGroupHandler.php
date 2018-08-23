<?php

/*
 * This file is part of the `idea` project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Messenger\Idea;

use App\Repository\IdeaRepository;
use Pagerfanta\Pagerfanta;

class GetIdeasByGroupHandler
{
    /**
     * @var IdeaRepository
     */
    private $repository;

    /**
     * GetIdeaPaginatorHandler constructor.
     *
     * @param IdeaRepository $repository
     */
    public function __construct(IdeaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(GetIdeasByGroupQuery $query): Pagerfanta
    {
        $page = $query->getPage();
        $group = $query->getGroup();

        return $this->repository->findByGroup($group, $page);
    }
}

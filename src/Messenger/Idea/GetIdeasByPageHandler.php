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

use App\MessageBus\QueryHandlerInterface;
use App\Repository\IdeaRepository;
use Pagerfanta\Pagerfanta;

class GetIdeasByPageHandler implements QueryHandlerInterface
{
    /**
     * @var IdeaRepository
     */
    private $repository;

    /**
     * GetIdeaPaginatorHandler constructor.
     */
    public function __construct(IdeaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(GetIdeasByPageQuery $query): Pagerfanta
    {
        $page = $query->getPage();
        $showPrivates = $query->getShowPrivates();

        return $this->repository->findLatest($page, $showPrivates);
    }
}

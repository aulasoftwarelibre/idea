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

namespace App\Controller\Idea;

use App\Message\Idea\GetIdeasByPageQuery;
use App\MessageBus\QueryBus;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function assert;
use function ceil;

/**
 * @Route("/idea", defaults={"page": "1"}, name="idea_index")
 * @Route("/idea/page/{page}", requirements={"page": "[1-9]\d*"}, name="idea_index_paginated")
 */
class ListIdeaController extends AbstractController
{
    public function __invoke(int $page, QueryBus $queryBus): Response
    {
        $ideas = $queryBus->query(
            new GetIdeasByPageQuery(
                $page,
                $this->isGranted('ROLE_ADMIN')
            )
        );
        assert($ideas instanceof Paginator);

        $itemsPerPage = $ideas->getQuery()->getMaxResults();
        $numPages     = ceil($ideas->count() / $itemsPerPage);

        return $this->render('frontend/idea/index.html.twig', [
            'ideas' => $ideas,
            'numPages' => $numPages,
            'page' => $page,
        ]);
    }
}

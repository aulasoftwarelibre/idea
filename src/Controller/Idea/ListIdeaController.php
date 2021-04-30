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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function assert;
use function ceil;
use function sprintf;

class ListIdeaController extends AbstractController
{
    /**
     * @Route("/api/ideas", name="api_ideas_get", options={"expose"=true}, methods={"GET"})
     */
    public function api(Request $request, QueryBus $queryBus): Response
    {
        $pattern = $request->query->getAlnum('q');

        $ideas = $queryBus->query(
            new GetIdeasByPageQuery(
                1,
                $this->isGranted('ROLE_ADMIN'),
                $pattern
            )
        );
        assert($ideas instanceof Paginator);

        return $this->json([
            'items' => $ideas->getIterator()->getArrayCopy(),
            'action' => [
                'url' => $this->generateUrl('idea_index', ['q' => $pattern]),
                'text' => sprintf('Ver los %d resultados', $ideas->count()),
            ],
        ]);
    }

    /**
     * @Route("/idea", defaults={"page": "1"}, name="idea_index")
     * @Route("/idea/page/{page}", requirements={"page": "[1-9]\d*"}, name="idea_index_paginated")
     */
    public function __invoke(Request $request, int $page, QueryBus $queryBus): Response
    {
        $pattern = $request->query->getAlnum('q');

        $ideas = $queryBus->query(
            new GetIdeasByPageQuery(
                $page,
                $this->isGranted('ROLE_ADMIN'),
                $pattern
            )
        );
        assert($ideas instanceof Paginator);

        $itemsPerPage = $ideas->getQuery()->getMaxResults();
        $numPages     = ceil($ideas->count() / $itemsPerPage);

        return $this->render('frontend/idea/index.html.twig', [
            'ideas' => $ideas,
            'numPages' => $numPages,
            'page' => $page,
            'pattern' => $pattern,
        ]);
    }
}

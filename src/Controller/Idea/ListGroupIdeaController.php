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

use App\Entity\Group;
use App\Entity\Idea;
use App\MessageBus\QueryBus;
use App\Message\Idea\GetIdeasByGroupQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/idea/group/{slug}", defaults={"page": "1"}, name="idea_group_index")
 * @Route("/idea/group/{slug}/page/{page}", requirements={"page": "[1-9]\d*"}, name="idea_group_index_paginated"))
 */
class ListGroupIdeaController extends AbstractController
{
    /**
     * @var QueryBus
     */
    private $queryBus;

    public function __construct(
        QueryBus $queryBus
    ) {
        $this->queryBus = $queryBus;
    }

    public function __invoke(Group $group, int $page): Response
    {
        $ideas = $this->queryBus->query(
            new GetIdeasByGroupQuery(
                $page,
                $group
            )
        );

        $itemsPerPage = $ideas->getQuery()->getMaxResults();
        $numPages = ceil($ideas->count() / $itemsPerPage);

        return $this->render('frontend/idea/index_group.html.twig', [
            'ideas' => $ideas,
            'group' => $group,
            'numPages' => $numPages,
            'page' => $page,
        ]);
    }
}

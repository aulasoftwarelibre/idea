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

use App\Entity\Idea;
use App\MessageBus\QueryBus;
use App\Messenger\Idea\GetIdeasByPageQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/idea", defaults={"page": "1"}, name="idea_index")
 * @Route("/idea/page/{page}", requirements={"page": "[1-9]\d*"}, name="idea_index_paginated")
 */
class ListIdeaController extends AbstractController
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

    public function __invoke(int $page): Response
    {
        $ideas = $this->queryBus->query(
            new GetIdeasByPageQuery(
                $page,
                $this->isGranted('ROLE_ADMIN')
            )
        );

        return $this->render('frontend/idea/index.html.twig', [
            'ideas' => $ideas,
        ]);
    }
}

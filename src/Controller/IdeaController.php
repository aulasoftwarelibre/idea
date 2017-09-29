<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Command\GetIdeasByGroupQuery;
use App\Command\GetIdeasByPageQuery;
use App\Entity\Group;
use App\Entity\Idea;
use League\Tactician\CommandBus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/idea")
 */
class IdeaController extends Controller
{
    /**
     * @var CommandBus
     */
    private $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @Route("/{slug}", name="idea_show")
     */
    public function showAction(Idea $idea)
    {
        return $this->render('frontend/idea/show.html.twig', [
            'idea' => $idea,
        ]);
    }

    /**
     * @Route("/", defaults={"page": "1"}, name="idea_index")
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="idea_index_paginated")
     */
    public function indexAction(int $page): Response
    {
        $ideas = $this->bus->handle(
            new GetIdeasByPageQuery(
                $page
            )
        );

        return $this->render('frontend/idea/index.html.twig', [
            'ideas' => $ideas,
        ]);
    }

    /**
     * @Route("/group/{slug}", defaults={"page": "1"}, name="idea_group_index")
     * @Route("/group/{slug}/page/{page}", requirements={"page": "[1-9]\d*"}, name="idea_group_index_paginated"))
     */
    public function indexByGroupAction(Group $group, int $page): Response
    {
        $ideas = $this->bus->handle(
            new GetIdeasByGroupQuery(
                $page,
                $group
            )
        );

        return $this->render('frontend/idea/index_group.html.twig', [
            'ideas' => $ideas,
            'group' => $group,
        ]);
    }
}

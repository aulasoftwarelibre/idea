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

use App\Command\AddIdeaCommand;
use App\Command\GetIdeasByGroupQuery;
use App\Command\GetIdeasByPageQuery;
use App\Command\UpdateIdeaCommand;
use App\Entity\Group;
use App\Entity\Idea;
use App\Form\Type\IdeaType;
use League\Tactician\CommandBus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/new", name="idea_new")
     * @Method({"GET", "POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(IdeaType::class, new Idea());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idea = $this->bus->handle(
                new AddIdeaCommand(
                    $form->getData()->getTitle(),
                    $form->getData()->getDescription(),
                    $this->getUser(),
                    $form->getData()->getGroup()
                )
            );

            $this->addFlash('positive', 'Idea creada con éxito');

            return $this->redirectToRoute('idea_show', ['slug' => $idea->getSlug()]);
        }

        return $this->render('frontend/idea/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="idea_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY') and is_granted('EDIT', idea)")
     */
    public function editAction(Idea $idea, Request $request)
    {
        $form = $this->createForm(IdeaType::class, $idea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idea = $this->bus->handle(
                new UpdateIdeaCommand(
                    $idea,
                    $form->getData()->getTitle(),
                    $form->getData()->getDescription(),
                    $form->getData()->getGroup()
                )
            );

            $this->addFlash('positive', 'Idea actualizada con éxito');

            return $this->redirectToRoute('idea_show', ['slug' => $idea->getSlug()]);
        }

        return $this->render('frontend/idea/edit.html.twig', [
            'form' => $form->createView(),
            'idea' => $idea,
        ]);
    }

    /**
     * @Route("/{slug}", name="idea_show")
     */
    public function showAction(Idea $idea)
    {
        return $this->render('frontend/idea/show.html.twig', [
            'complete' => true,
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

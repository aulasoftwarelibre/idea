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
use App\Command\AddVoteCommand;
use App\Command\ApproveIdeaCommand;
use App\Command\CloseIdeaCommand;
use App\Command\GetIdeasByGroupQuery;
use App\Command\GetIdeasByPageQuery;
use App\Command\RejectIdeaCommand;
use App\Command\RemoveVoteCommand;
use App\Command\UpdateIdeaCommand;
use App\Entity\Group;
use App\Entity\Idea;
use App\Event\IdeaWasApprovedEvent;
use App\Event\IdeaWasCreatedEvent;
use App\Event\IdeaWasVotedEvent;
use App\Exception\NoMoreSeatsLeftException;
use App\Form\Type\IdeaType;
use League\Tactician\CommandBus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(CommandBus $bus, EventDispatcherInterface $eventDispatcher)
    {
        $this->bus = $bus;
        $this->eventDispatcher = $eventDispatcher;
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

            $this->eventDispatcher->dispatch(IdeaWasCreatedEvent::class,
                new IdeaWasCreatedEvent(
                    $idea
                )
            );

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
     * @Route("/{slug}/join", name="idea_join", options={"expose"=true})
     * @Method({"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function joinAction(Idea $idea, Request $request)
    {
        try {
            $this->bus->handle(
                new AddVoteCommand(
                    $idea,
                    $this->getUser()
                )
            );

            $this->addFlash('positive', 'Te has unido con éxito a la propuesta.');

            $this->eventDispatcher->dispatch(IdeaWasVotedEvent::class,
                new IdeaWasVotedEvent(
                    $idea,
                    $this->getUser()
                )
            );
        } catch (NoMoreSeatsLeftException $e) {
            $this->addFlash('negative', 'No quedan plazas libres');
        }

        return $this->redirectToRoute('idea_show', ['slug' => $idea->getSlug()]);
    }

    /**
     * @Route("/{slug}/leave", name="idea_leave", options={"expose"=true})
     * @Method({"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function leaveAction(Idea $idea, Request $request)
    {
        $this->bus->handle(
            new RemoveVoteCommand(
                $idea,
                $this->getUser()
            )
        );
        $this->addFlash('positive', 'Te has salido con éxito de la propuesta.');

        return $this->redirectToRoute('idea_show', ['slug' => $idea->getSlug()]);
    }

    /**
     * @Route("/{slug}/open", name="idea_open", options={"expose"=true})
     * @Method({"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function openAction(Idea $idea, Request $request)
    {
        $this->bus->handle(
            new CloseIdeaCommand(
                $idea,
                false
            )
        );
        $this->addFlash('positive', 'La idea se ha abierto correctamente.');

        return $this->redirectToRoute('idea_show', ['slug' => $idea->getSlug()]);
    }

    /**
     * @Route("/{slug}/close", name="idea_close", options={"expose"=true})
     * @Method({"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function closeAction(Idea $idea, Request $request)
    {
        $this->bus->handle(
            new CloseIdeaCommand(
                $idea,
                true
            )
        );
        $this->addFlash('positive', 'La idea se ha cerrado correctamente.');

        return $this->redirectToRoute('idea_show', ['slug' => $idea->getSlug()]);
    }

    /**
     * @Route("/{slug}/approve", name="idea_approve", options={"expose"=true})
     * @Method({"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function approveAction(Idea $idea, Request $request)
    {
        $this->bus->handle(
            new ApproveIdeaCommand(
                $idea
            )
        );
        $this->addFlash('positive', 'La idea se ha aprobado correctamente.');

        $this->get('event_dispatcher')->dispatch(IdeaWasApprovedEvent::class,
            new IdeaWasApprovedEvent(
                $idea
            )
        );

        return $this->redirectToRoute('idea_show', ['slug' => $idea->getSlug()]);
    }

    /**
     * @Route("/{slug}/reject", name="idea_reject", options={"expose"=true})
     * @Method({"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function rejectAction(Idea $idea, Request $request)
    {
        $this->bus->handle(
            new RejectIdeaCommand(
                $idea
            )
        );
        $this->addFlash('positive', 'La idea se ha rechazado correctamente.');

        return $this->redirectToRoute('idea_show', ['slug' => $idea->getSlug()]);
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

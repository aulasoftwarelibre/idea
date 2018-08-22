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

use App\Entity\Group;
use App\Entity\Idea;
use App\Event\IdeaWasApprovedEvent;
use App\Event\IdeaWasCreatedEvent;
use App\Event\IdeaWasVotedEvent;
use App\Exception\NoMoreSeatsLeftException;
use App\Form\Type\IdeaType;
use App\Messenger\Idea\AddIdeaCommand;
use App\Messenger\Idea\ApproveIdeaCommand;
use App\Messenger\Idea\CloseIdeaCommand;
use App\Messenger\Idea\GetIdeasByGroupQuery;
use App\Messenger\Idea\GetIdeasByPageQuery;
use App\Messenger\Idea\RejectIdeaCommand;
use App\Messenger\Idea\UpdateIdeaCommand;
use App\Messenger\Vote\AddVoteCommand;
use App\Messenger\Vote\RemoveVoteCommand;
use App\Repository\IdeaRepository;
use Leogout\Bundle\SeoBundle\Provider\SeoGeneratorProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/idea")
 */
class IdeaController extends Controller
{
    /**
     * @var MessageBusInterface
     */
    private $bus;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var SeoGeneratorProvider
     */
    private $seoGeneratorProvider;

    public function __construct(
        MessageBusInterface $bus,
        EventDispatcherInterface $eventDispatcher,
        SeoGeneratorProvider $seoGeneratorProvider
    ) {
        $this->bus = $bus;
        $this->eventDispatcher = $eventDispatcher;
        $this->seoGeneratorProvider = $seoGeneratorProvider;
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
            $idea = $this->bus->dispatch(
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
            $idea = $this->bus->dispatch(
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
            $this->bus->dispatch(
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

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{slug}/leave", name="idea_leave", options={"expose"=true})
     * @Method({"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function leaveAction(Idea $idea, Request $request)
    {
        $this->bus->dispatch(
            new RemoveVoteCommand(
                $idea,
                $this->getUser()
            )
        );
        $this->addFlash('positive', 'Te has salido con éxito de la propuesta.');

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{slug}/open", name="idea_open", options={"expose"=true})
     * @Method({"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function openAction(Idea $idea, Request $request)
    {
        $this->bus->dispatch(
            new CloseIdeaCommand(
                $idea,
                false
            )
        );
        $this->addFlash('positive', 'La idea se ha abierto correctamente.');

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{slug}/close", name="idea_close", options={"expose"=true})
     * @Method({"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function closeAction(Idea $idea, Request $request)
    {
        $this->bus->dispatch(
            new CloseIdeaCommand(
                $idea,
                true
            )
        );
        $this->addFlash('positive', 'La idea se ha cerrado correctamente.');

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{slug}/approve", name="idea_approve", options={"expose"=true})
     * @Method({"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function approveAction(Idea $idea, Request $request)
    {
        $this->bus->dispatch(
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

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{slug}/reject", name="idea_reject", options={"expose"=true})
     * @Method({"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function rejectAction(Idea $idea, Request $request)
    {
        $this->bus->dispatch(
            new RejectIdeaCommand(
                $idea
            )
        );
        $this->addFlash('positive', 'La idea se ha rechazado correctamente.');

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{slug}", name="idea_show")
     */
    public function showAction(Idea $idea)
    {
        $title = $idea->getTitle();
        $description = mb_substr(strip_tags($idea->getDescription()), 0, 200);

        $this->seoGeneratorProvider
            ->get('basic')
            ->setTitle($title)
            ->setDescription($description)
        ;

        $this->seoGeneratorProvider
            ->get('og')
            ->setTitle($title)
            ->setDescription($description)
        ;

        $this->seoGeneratorProvider
            ->get('twitter')
            ->setTitle($title)
            ->setDescription($description)
        ;

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
        $ideas = $this->bus->dispatch(
            new GetIdeasByPageQuery(
                $page,
                $this->isGranted('ROLE_ADMIN')
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
        $ideas = $this->bus->dispatch(
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

    public function getMoreVotesPendingIdeas(IdeaRepository $ideaRepository)
    {
        $ideas = $ideaRepository->findFilteredByVotes();

        return $this->render('frontend/idea/_ideas_block.html.twig', [
            'title' => 'Pendientes con más votos',
            'ideas' => $ideas,
        ]);
    }
}

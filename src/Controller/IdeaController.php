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

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Idea;
use App\Event\IdeaWasApprovedEvent;
use App\Event\IdeaWasCreatedEvent;
use App\Event\IdeaWasVotedEvent;
use App\Exception\NoMoreSeatsLeftException;
use App\Form\Type\IdeaType;
use App\MessageBus\CommandBus;
use App\MessageBus\QueryBus;
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
use Leogout\Bundle\SeoBundle\Seo\Basic\BasicSeoGenerator;
use Leogout\Bundle\SeoBundle\Seo\Og\OgSeoGenerator;
use Leogout\Bundle\SeoBundle\Seo\Twitter\TwitterSeoGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/idea")
 */
class IdeaController extends AbstractController
{
    /**
     * @var CommandBus
     */
    private $commandBus;
    /**
     * @var QueryBus
     */
    private $queryBus;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var SeoGeneratorProvider
     */
    private $seoGeneratorProvider;

    public function __construct(
        CommandBus $commandBus,
        QueryBus $queryBus,
        EventDispatcherInterface $eventDispatcher,
        SeoGeneratorProvider $seoGeneratorProvider
    ) {
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
        $this->eventDispatcher = $eventDispatcher;
        $this->seoGeneratorProvider = $seoGeneratorProvider;
    }

    /**
     * @Route("/new", name="idea_new", methods={"GET", "POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function newAction(Request $request): Response
    {
        $form = $this->createForm(IdeaType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idea = $this->commandBus->dispatch(
                new AddIdeaCommand(
                    $form->getData()->getTitle(),
                    $form->getData()->getDescription(),
                    $this->getUser(),
                    $form->getData()->getGroup()
                )
            );

            $this->addFlash('positive', 'Idea creada con éxito');

            $this->eventDispatcher->dispatch(
                IdeaWasCreatedEvent::class,
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
     * @Route("/{slug}/edit", name="idea_edit", methods={"GET", "POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY') and is_granted('EDIT', idea)")
     */
    public function editAction(Idea $idea, Request $request): Response
    {
        $form = $this->createForm(IdeaType::class, $idea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idea = $this->commandBus->dispatch(
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
     * @Route("/{slug}/join", name="idea_join", options={"expose"=true}, methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY') && is_granted('IIDEA_JOIN')")
     */
    public function joinAction(Idea $idea, Request $request): Response
    {
        try {
            $this->commandBus->dispatch(
                new AddVoteCommand(
                    $idea,
                    $this->getUser()
                )
            );

            $this->addFlash('positive', 'Te has unido con éxito a la propuesta.');

            $this->eventDispatcher->dispatch(
                IdeaWasVotedEvent::class,
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
     * @Route("/{slug}/leave", name="idea_leave", options={"expose"=true}, methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function leaveAction(Idea $idea, Request $request): Response
    {
        $this->commandBus->dispatch(
            new RemoveVoteCommand(
                $idea,
                $this->getUser()
            )
        );
        $this->addFlash('positive', 'Te has salido con éxito de la propuesta.');

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{slug}/open", name="idea_open", options={"expose"=true}, methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function openAction(Idea $idea, Request $request): Response
    {
        $this->commandBus->dispatch(
            new CloseIdeaCommand(
                $idea,
                false
            )
        );
        $this->addFlash('positive', 'La idea se ha abierto correctamente.');

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{slug}/close", name="idea_close", options={"expose"=true}, methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function closeAction(Idea $idea, Request $request): Response
    {
        $this->commandBus->dispatch(
            new CloseIdeaCommand(
                $idea,
                true
            )
        );
        $this->addFlash('positive', 'La idea se ha cerrado correctamente.');

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{slug}/approve", name="idea_approve", options={"expose"=true}, methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function approveAction(Idea $idea, Request $request): Response
    {
        $this->commandBus->dispatch(
            new ApproveIdeaCommand(
                $idea
            )
        );
        $this->addFlash('positive', 'La idea se ha aprobado correctamente.');

        $this->get('event_dispatcher')->dispatch(
            IdeaWasApprovedEvent::class,
            new IdeaWasApprovedEvent(
                $idea
            )
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{slug}/reject", name="idea_reject", options={"expose"=true}, methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function rejectAction(Idea $idea, Request $request): Response
    {
        $this->commandBus->dispatch(
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
    public function showAction(Idea $idea): Response
    {
        $title = $idea->getTitle();
        $description = mb_substr(strip_tags($idea->getDescription()), 0, 200);

        /** @var BasicSeoGenerator $basicSeoGenerator */
        $basicSeoGenerator = $this->seoGeneratorProvider->get('basic');
        $basicSeoGenerator
            ->setTitle($title)
            ->setDescription($description);

        /** @var OgSeoGenerator $ogSeoGenerator */
        $ogSeoGenerator = $this->seoGeneratorProvider->get('og');
        $ogSeoGenerator
            ->setTitle($title)
            ->setDescription($description);

        /** @var TwitterSeoGenerator $twitterSeoGenerator */
        $twitterSeoGenerator = $this->seoGeneratorProvider->get('twitter');
        $twitterSeoGenerator
            ->setTitle($title)
            ->setDescription($description);

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

    /**
     * @Route("/group/{slug}", defaults={"page": "1"}, name="idea_group_index")
     * @Route("/group/{slug}/page/{page}", requirements={"page": "[1-9]\d*"}, name="idea_group_index_paginated"))
     */
    public function indexByGroupAction(Group $group, int $page): Response
    {
        $ideas = $this->queryBus->query(
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

    public function getMoreVotesPendingIdeas(IdeaRepository $ideaRepository): Response
    {
        $ideas = $ideaRepository->findFilteredByVotes();

        return $this->render('frontend/idea/_pending_ideas_block.html.twig', [
            'title' => 'Pendientes con más votos',
            'ideas' => $ideas,
        ]);
    }

    public function getNextScheduledIdeas(IdeaRepository $ideaRepository): Response
    {
        $ideas = $ideaRepository->findNextScheduled();

        return $this->render('frontend/idea/_scheduled_ideas_block.html.twig', [
            'title' => 'Próximas actividades',
            'ideas' => $ideas,
        ]);
    }
}

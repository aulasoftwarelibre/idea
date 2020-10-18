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
use App\Event\IdeaWasApprovedEvent;
use App\Message\Idea\ApproveIdeaCommand;
use App\MessageBus\CommandBus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/idea/{slug}/approve", name="idea_approve", options={"expose"=true}, methods={"POST"})
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ApproveIdeaController extends AbstractController
{
    private CommandBus $commandBus;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        CommandBus $commandBus,
        EventDispatcherInterface $dispatcher
    ) {
        $this->commandBus = $commandBus;
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(Idea $idea): Response
    {
        $this->commandBus->dispatch(
            new ApproveIdeaCommand(
                $idea
            )
        );

        $this->dispatcher->dispatch(
            new IdeaWasApprovedEvent(
                $idea
            )
        );

        $this->addFlash('positive', 'La idea se ha aprobado correctamente.');

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}

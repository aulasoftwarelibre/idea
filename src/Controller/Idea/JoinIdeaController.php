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
use App\Entity\User;
use App\Event\IdeaWasVotedEvent;
use App\Exception\NoMoreSeatsLeftException;
use App\MessageBus\CommandBus;
use App\Message\Vote\AddVoteCommand;
use App\Security\Voter\JoinIdeaVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/idea/{slug}/join", name="idea_join", options={"expose"=true}, methods={"POST"})
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class JoinIdeaController extends AbstractController
{
    /**
     * @var CommandBus
     */
    private $commandBus;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        CommandBus $commandBus,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->commandBus = $commandBus;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(Idea $idea, Request $request): Response
    {
        $this->denyAccessUnlessGranted(JoinIdeaVoter::JOIN, $idea);

        try {
            /** @var User $user */
            $user = $this->getUser();

            $this->commandBus->dispatch(
                new AddVoteCommand(
                    $idea,
                    $user
                )
            );

            $this->eventDispatcher->dispatch(
                new IdeaWasVotedEvent(
                    $idea,
                    $user
                )
            );

            $this->addFlash('positive', 'Te has unido con éxito a la propuesta.');
        } catch (NoMoreSeatsLeftException $e) {
            $this->addFlash('negative', 'No quedan plazas libres');
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}

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
use App\MessageBus\CommandBus;
use App\Messenger\Vote\RemoveVoteCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/idea/{slug}/leave", name="idea_leave", options={"expose"=true}, methods={"POST"})
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class LeaveIdeaController extends AbstractController
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(
        CommandBus $commandBus
    ) {
        $this->commandBus = $commandBus;
    }

    public function __invoke(Idea $idea, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $this->commandBus->dispatch(
            new RemoveVoteCommand(
                $idea,
                $user
            )
        );

        $this->addFlash('positive', 'Te has salido con éxito de la propuesta.');

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}

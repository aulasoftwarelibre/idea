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
use App\Message\Idea\CloseIdeaCommand;
use App\MessageBus\CommandBus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/idea/{slug}/close", name="idea_close", options={"expose"=true}, methods={"POST"})
 * @Security("is_granted('ROLE_ADMIN')")
 */
class CloseIdeaController extends AbstractController
{
    private CommandBus $commandBus;

    public function __construct(
        CommandBus $commandBus
    ) {
        $this->commandBus = $commandBus;
    }

    public function __invoke(Idea $idea): Response
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
}

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
use App\Message\Email\SendEmailCommand;
use App\Message\Idea\OpenIdeaJitsiRoomCommand;
use App\MessageBus\CommandBus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function sprintf;

/**
 * @Route("/idea/{slug}/jitsi", name="idea_open_jitsi", methods={"POST"})
 * @Security("is_granted('ROLE_ADMIN')")
 */
final class OpenIdeaJitsiRoomController extends AbstractController
{
    private CommandBus $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(Idea $idea): Response
    {
        $this->commandBus->dispatch(
            new OpenIdeaJitsiRoomCommand($idea)
        );

        $ideaStartsAt = $idea->getStartsAt()->format('H:i');

        $message = sprintf('<p>¡Hola!</p>
<p>Te escribimos para informarte de que la sala de la videoconferencia ya está disponible.</p>
<p>Puedes acceder desde la página de la actividad. La charla empecerá a las %s horas. ¡Te esperamos!</p>', $ideaStartsAt);

        $this->commandBus->dispatch(
            new SendEmailCommand(
                $idea->getId(),
                $message,
                false
            )
        );

        return $this->redirectToRoute('idea_jitsi', [
            'slug' => $idea->getSlug(),
        ]);
    }
}

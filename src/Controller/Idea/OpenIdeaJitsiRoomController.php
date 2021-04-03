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
use App\MessageBus\CommandBus;
use App\Messenger\Idea\OpenIdeaJitsiRoomCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/idea/{slug}/jitsi", name="idea_open_jitsi", methods={"POST"})
 * @Security("is_granted('ROLE_ADMIN')")
 */
final class OpenIdeaJitsiRoomController extends AbstractController
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(Idea $idea): Response
    {
        $this->commandBus->dispatch(
            new OpenIdeaJitsiRoomCommand($idea)
        );

        return $this->redirectToRoute('idea_jitsi', [
            'slug' => $idea->getSlug(),
        ]);
    }
}

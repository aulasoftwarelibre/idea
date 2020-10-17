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

use App\Entity\User;
use App\Event\IdeaWasCreatedEvent;
use App\Form\Type\IdeaType;
use App\Message\Idea\AddIdeaCommand;
use App\MessageBus\CommandBus;
use App\Security\Voter\AddIdeaVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function assert;

/**
 * @Route("/idea/new", name="idea_new", methods={"GET", "POST"})
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class NewIdeaController extends AbstractController
{
    private CommandBus $commandBus;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        CommandBus $commandBus,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->commandBus      = $commandBus;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted(AddIdeaVoter::ADD);

        $form = $this->createForm(IdeaType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            assert($user instanceof User);
            $idea = $this->commandBus->dispatch(
                new AddIdeaCommand(
                    $form->getData()->getTitle(),
                    $form->getData()->getDescription(),
                    $user,
                    $form->getData()->getGroup()
                )
            );

            $this->addFlash('positive', 'Idea creada con éxito');

            $this->eventDispatcher->dispatch(
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
}

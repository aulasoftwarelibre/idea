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
use App\Form\Type\IdeaType;
use App\Message\Idea\UpdateIdeaCommand;
use App\MessageBus\CommandBus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/idea/{slug}/edit", name="idea_edit", methods={"GET", "POST"})
 * @Security("is_granted('GROUP_MEMBER', idea.getGroup()) or is_granted('EDIT', idea)")
 */
class EditIdeaController extends AbstractController
{
    private CommandBus $commandBus;

    public function __construct(
        CommandBus $commandBus
    ) {
        $this->commandBus = $commandBus;
    }

    public function __invoke(Idea $idea, Request $request): Response
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
}

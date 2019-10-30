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

use App\Entity\Idea;
use App\Form\Dto\IdeaMessageDto;
use App\Form\Type\IdeaMessageType;
use App\MessageBus\CommandBus;
use App\Messenger\IdeaMessage\SendIdeaMessageCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IdeaSendMessageController extends AbstractController
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/idea/{slug}/message", name="idea_send_message", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function __invoke(Idea $idea, Request $request): Response
    {
        $form = $this->createForm(IdeaMessageType::class, new IdeaMessageDto());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var IdeaMessageDto $message */
            $message = $form->getData();

            $this->commandBus->dispatch(
                new SendIdeaMessageCommand(
                    $idea->getId(),
                    $message->getMessage(),
                    $message->getIsTest()
                )
            );

            if (false === $message->getIsTest()) {
                $this->addFlash('positive', 'Mensaje enviado');

                return $this->redirectToRoute('idea_show', ['slug' => $idea->getSlug()]);
            }

            $this->addFlash('positive', 'Prueba enviada');
        }

        return $this->render('frontend/idea/message.html.twig', [
            'form' => $form->createView(),
            'idea' => $idea,
        ]);
    }
}

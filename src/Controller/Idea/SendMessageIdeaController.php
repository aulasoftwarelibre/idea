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
use App\Form\Dto\IdeaMessageDto;
use App\Form\Type\IdeaMessageType;
use App\Message\Email\SendEmailCommand;
use App\MessageBus\CommandBus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function assert;

#[Route(path: '/idea/{slug}/message', name: 'idea_send_message', methods: ['GET', 'POST'])]
#[Security("is_granted('ROLE_ADMIN')")]
class SendMessageIdeaController extends AbstractController
{
    public function __construct(private CommandBus $commandBus)
    {
    }

    public function __invoke(Idea $idea, Request $request): Response
    {
        $form = $this->createForm(IdeaMessageType::class, new IdeaMessageDto());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
            assert($message instanceof IdeaMessageDto);

            $this->commandBus->dispatch(
                new SendEmailCommand(
                    $idea->getId(),
                    $message->getMessage(),
                    $message->getIsTest(),
                ),
            );

            if ($message->getIsTest() === false) {
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

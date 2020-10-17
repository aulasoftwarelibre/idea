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

namespace App\Controller\Profile;

use App\Entity\User;
use App\Form\Type\RegisterType;
use App\Message\LogPolicy\CheckUserAcceptLastPolicyVersionQuery;
use App\Message\LogPolicy\UserAcceptedLastPolicyVersionCommand;
use App\MessageBus\CommandBus;
use App\MessageBus\QueryBus;
use Doctrine\ORM\OptimisticLockException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function assert;

/**
 * @Route("/profile/register", name="profile_register", methods={"GET", "POST"})
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class RegisterProfileController extends AbstractController
{
    public function __invoke(Request $request, CommandBus $commandBus, QueryBus $queryBus): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        $this->checkUserHasAcceptedTerms($queryBus, $user);

        $manager = $this->getDoctrine()->getManager();

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $commandBus->dispatch(
                    new UserAcceptedLastPolicyVersionCommand($user)
                );

                $manager->persist($user);
                $manager->flush();

                $this->addFlash('positive', 'Su perfil se ha creado correctamente');

                return $this->redirectToRoute('homepage');
            }
        } catch (OptimisticLockException $e) {
            $this->addFlash('error', 'Error al registrar. Intentelo de nuevo');
        }

        return $this->render('/frontend/profile/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function checkUserHasAcceptedTerms(QueryBus $queryBus, User $user): void
    {
        $userHasAccepted = $queryBus->query(
            new CheckUserAcceptLastPolicyVersionQuery($user)
        );

        if ($userHasAccepted) {
            throw $this->createNotFoundException();
        }
    }
}

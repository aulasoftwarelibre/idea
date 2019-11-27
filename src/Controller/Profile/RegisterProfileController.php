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
use App\MessageBus\CommandBus;
use App\Messenger\LogPolicy\UserAcceptedLastPolicyVersionCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/register", name="profile_register", methods={"GET", "POST"})
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class RegisterProfileController extends AbstractController
{
    public function __invoke(Request $request, CommandBus $commandBus): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        $manager = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setHasProfile(true);

            $commandBus->dispatch(
                new UserAcceptedLastPolicyVersionCommand($user)
            );

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('positive', 'Su perfil se ha creado correctamente');

            return $this->redirectToRoute('homepage');
        }
        $manager->detach($user);

        return $this->render('/frontend/profile/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

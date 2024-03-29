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
use App\Form\Type\ProfileType;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function assert;

#[Route(path: '/profile/edit', name: 'profile_edit', methods: ['GET', 'POST'])]
#[Security("is_granted('IS_AUTHENTICATED_FULLY')")]
class EditProfileController extends AbstractController
{
    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }

    public function __invoke(Request $request): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $form = $this->createForm(ProfileType::class, $user);

        $form->handleRequest($request);

        $manager = $this->managerRegistry->getManager();

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('positive', 'Su perfil ha sido actualizado');

                return $this->redirectToRoute('homepage');
            }
        } catch (OptimisticLockException) {
            $this->addFlash('error', 'Error al guardar. Intentelo de nuevo');
        }

        return $this->render('/frontend/profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

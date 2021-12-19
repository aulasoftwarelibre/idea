<?php

declare(strict_types=1);

namespace App\Controller\Group;

use App\Entity\Group;
use App\Form\Type\GroupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/group/{slug}", name="group_edit")
 */
class EditGroupController extends AbstractController
{
    public function __invoke(Group $group, Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('GROUP_MEMBER', $group);

        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash('positive', 'Perfil actualizado');

            return $this->redirectToRoute('group_edit', ['slug' => $group->getSlug()]);
        }

        return $this->render('frontend/group/index.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }
}

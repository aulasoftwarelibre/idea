<?php


namespace App\Controller\Profile;


use App\Entity\Group;
use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

final class ShowGroupMenuEmbedController extends AbstractController
{
    public function __invoke(RequestStack $requestStack, GroupRepository $groupRepository): Response
    {
        $icon = 'home';
        $name = 'Inicio';

        $attributes = $requestStack->getMasterRequest()->attributes;

        if ($attributes->has('group')) {
            /** @var Group $group */
            $group = $attributes->get('group');
            $icon = $group->getIcon();
            $name = $group->getName();
        }


        return $this->render('frontend/profile/_group_menu.html.twig', [
            'groups' => $groupRepository->findAll(),
            'icon' => $icon,
            'name' => $name,
        ]);
    }
}

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

use App\Entity\Group;
use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

use function assert;

final class ShowGroupMenuEmbedController extends AbstractController
{
    public function __invoke(RequestStack $requestStack, GroupRepository $groupRepository): Response
    {
        $icon = 'home';
        $name = 'Inicio';

        if ($requestStack->getMasterRequest() !== null) {
            $attributes = $requestStack->getMasterRequest()->attributes;

            if ($attributes->has('group')) {
                $group = $attributes->get('group');
                assert($group instanceof Group);
                $icon = $group->getIcon();
                $name = $group->getName();
            }
        }

        return $this->render('frontend/profile/_group_menu.html.twig', [
            'groups' => $groupRepository->findAll(),
            'icon' => $icon,
            'name' => $name,
        ]);
    }
}

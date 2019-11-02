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

namespace App\Menu;

use App\Repository\GroupRepository;
use App\Security\Voter\AddIdeaVoter;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Builder
{
    /**
     * @var FactoryInterface
     */
    private $factory;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var GroupRepository
     */
    private $groupRepository;

    public function __construct(
        FactoryInterface $factory,
        AuthorizationCheckerInterface $authorizationChecker,
        GroupRepository $groupRepository
    ) {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->groupRepository = $groupRepository;
    }

    public function mainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Inicio', ['route' => 'idea_index']);

        if ($this->authorizationChecker->isGranted(AddIdeaVoter::ADD)) {
            $menu->addChild('Añadir idea', ['route' => 'idea_new']);
        }

        $groups = $menu->addChild('Grupos');
        foreach ($this->groupRepository->findAll() as $group) {
            $groups->addChild($group->getName(), ['route' => 'idea_group_index', 'routeParameters' => ['slug' => $group->getSlug()]]);
        }

        $menu->addChild('Ayuda', ['route' => 'help']);

        return $menu;
    }

    public function sidebarMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Inicio', ['route' => 'homepage'])->setExtra('icon', 'home');
        $menu->addChild('Ayuda', ['route' => 'help']);

        if ($this->authorizationChecker->isGranted('ROLE_USER')) {
            $menu->addChild('Añadir idea', ['route' => 'idea_new'])->setExtra('icon', 'add');
        }

        $groups = $menu->addChild('groups', [
            'label' => 'Grupos',
            'extras' => ['dropdown' => false, 'submenu' => true],
        ])->setAttribute('class', 'header');

        foreach ($this->groupRepository->findAll() as $idx => $group) {
            $groups->addChild("group-{$idx}", [
                'label' => $group->getName(),
                'route' => 'idea_group_index',
                'routeParameters' => ['slug' => $group->getSlug()],
            ]);
        }

        return $menu;
    }
}

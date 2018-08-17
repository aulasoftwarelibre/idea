<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Builder
{
    /**
     * @var FactoryInterface
     */
    private $factory;
    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * Builder constructor.
     */
    public function __construct(
        FactoryInterface $factory,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function mainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'ui large secondary pointing menu');
        $menu->addChild('Inicio', ['route' => 'idea_index']);

        if ($this->authorizationChecker->isGranted('ROLE_USER')) {
            $menu->addChild('Añadir idea', ['route' => 'idea_new']);
        }

        return $menu;
    }

    public function followingMenu(array $options)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Inicio', ['route' => 'homepage'])->setExtra('icon', 'home');

        if ($this->authorizationChecker->isGranted('ROLE_USER')) {
            $menu->addChild('Añadir idea', ['route' => 'idea_new'])->setExtra('icon', 'add');
        }

        return $menu;
    }
}

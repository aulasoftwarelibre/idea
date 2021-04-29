<?php

declare(strict_types=1);

namespace App\Menu;

use App\Entity\User;
use App\Repository\GroupRepository;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use function in_array;
use function sprintf;

final class MenuBuilder
{
    private FactoryInterface $factory;
    private TokenStorageInterface $token;
    private GroupRepository $groupRepository;

    public function __construct(
        FactoryInterface $factory,
        TokenStorageInterface $token,
        GroupRepository $groupRepository
    ) {
        $this->factory         = $factory;
        $this->token           = $token;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param array<string,mixed> $options
     */
    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('<i class="home icon"></i> Inicio', ['route' => 'idea_index'])
        ->setExtra('safe_label', true);

        $user = $this->token->getToken() ? $this->token->getToken()->getUser() : null;
        if ($user instanceof User && ! $user->isExternal()) {
            $menu->addChild('<i class="lightbulb icon"></i> Añadir idea', ['route' => 'idea_new'])
                ->setExtra('safe_label', true);
        }

        return $menu;
    }

    /**
     * @param array<string,mixed> $options
     */
    public function createGroupMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $groups = $this->groupRepository->findAll();

        foreach ($groups as $group) {
            $name = sprintf(
                '<i class="%s icon"></i><span class="name">%s</span>',
                $group->getIcon(),
                $group->getName()
            );
            $menu->addChild($name, [
                'route' => 'idea_group_index',
                'routeParameters' => ['slug' => $group->getSlug()],
            ])
                ->setExtra('safe_label', true);
        }

        return $menu;
    }

    /**
     * @param array<string,mixed> $options
     */
    public function profileMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $user = $this->token->getToken() ? $this->token->getToken()->getUser() : null;
        if ($user instanceof User && in_array('ROLE_ADMIN', $user->getRoles())) {
            $menu->addChild('<i class="lock icon"></i> Administrar', ['route' => 'admin'])
                ->setExtra('safe_label', true);
        }

        $menu->addChild('<i class="edit icon"></i> Editar perfil', ['route' => 'profile_edit'])
            ->setExtra('safe_label', true);

        $menu->addChild('<i class="user icon"></i> Ver curriculum', ['route' => 'profile_show'])
            ->setExtra('safe_label', true);

        $menu->addChild('<i class="sign out icon"></i> Salir', ['route' => 'logout'])
            ->setExtra('safe_label', true);

        return $menu;
    }
}

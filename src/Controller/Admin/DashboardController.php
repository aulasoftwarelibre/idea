<?php

namespace App\Controller\Admin;

use App\Entity\Degree;
use App\Entity\Group;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     * @Route("/admin", name="sonata_admin_dashboard")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Idea');
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Organization');
        yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Groups', 'fas fa-users', Group::class);
        yield MenuItem::linkToCrud('Degrees', 'fas fa-graduation-cap', Degree::class);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userMenu = parent::configureUserMenu($user);

        /** @var User $user */
        if (!$user->getImage()) {
            return $userMenu;
        }

        return $userMenu
            ->setAvatarUrl('/images/avatars/'.$user->getImage()->getName())
        ;
    }
}

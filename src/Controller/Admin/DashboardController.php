<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Activity;
use App\Entity\Degree;
use App\Entity\Group;
use App\Entity\Idea;
use App\Entity\Participation;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    #[Route(path: '/admin', name: 'admin')]
    public function index(): Response
    {
        $ideaCrudControllerUrl = $this->adminUrlGenerator->setController(IdeaCrudController::class)->generateUrl();

        return $this->redirect($ideaCrudControllerUrl);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Idea');
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    /** @inheritDoc */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Frontend', 'far fa-lightbulb', 'homepage');
        yield MenuItem::section('Organization');
        yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Groups', 'fas fa-users', Group::class);
        yield MenuItem::linkToCrud('Degrees', 'fas fa-graduation-cap', Degree::class);
        yield MenuItem::section('Activities');
        yield MenuItem::linkToCrud('Ideas', 'fas fa-lightbulb', Idea::class);
        yield MenuItem::linkToCrud('Activities', 'fas fa-wrench', Activity::class);
        yield MenuItem::linkToCrud('Inscriptions', 'fas fa-ticket-alt', Participation::class);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userMenu = parent::configureUserMenu($user);

        if (! $user instanceof User || ! $user->getImage()) {
            return $userMenu;
        }

        return $userMenu
            ->setAvatarUrl('/images/avatars/' . $user->getImage()->getName());
    }
}

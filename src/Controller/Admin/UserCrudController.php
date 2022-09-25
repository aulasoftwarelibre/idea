<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private Security $security,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $impersonate = Action::new('impersonate')
            ->linkToUrl(function (User $user) {
                return $this->urlGenerator->generate('homepage', [
                    '_switch_user' => $user->getUsername(),
                ], UrlGeneratorInterface::ABSOLUTE_URL);
            })
            ->displayIf(fn (User $user) => $this->security->isGranted('ROLE_ALLOWED_TO_SWITCH'));

        return $actions
            ->add(Crud::PAGE_INDEX, $impersonate);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Users')
            ->setEntityLabelInSingular('User');
    }

    /** @inheritDoc */
    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('General');
        yield TextField::new('username');
        yield TextField::new('email')
            ->hideOnIndex();

        yield FormField::addPanel('Security');
        yield BooleanField::new('enabled');
        yield ChoiceField::new('roles')
            ->setChoices([
                'Usuario' => 'ROLE_USER',
                'Gestor' => 'ROLE_ADMIN',
                'Administrador' => 'ROLE_SUPER_ADMIN',
            ])
            ->allowMultipleChoices();

        yield AssociationField::new('groups')
            ->hideOnIndex();

        yield AssociationField::new('versions')
            ->setLabel('Policies')
            ->setTemplatePath('admin/user/policies.html.twig')
            ->onlyOnDetail();

        yield FormField::addPanel('Profile');
        yield IdField::new('id')
            ->onlyOnIndex();

        yield TextField::new('firstname');
        yield TextField::new('lastname');
        yield ChoiceField::new('collective')
            ->setChoices(User::getCollectives());

        yield TextField::new('nic')
            ->hideOnIndex();

        yield AssociationField::new('degree')
            ->hideOnIndex();

        yield TextField::new('year')
            ->hideOnIndex();

        yield TextareaField::new('imageFile')
            ->setFormType(VichImageType::class)
            ->onlyOnForms();

        yield ImageField::new('image.name')
            ->setBasePath('/images/avatars')
            ->setCssClass('ea-vich-image')
            ->onlyOnDetail();
    }
}

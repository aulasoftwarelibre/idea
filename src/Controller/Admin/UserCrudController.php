<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Users')
            ->setEntityLabelInSingular('User')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('General');
        yield TextField::new('username');
        yield TextField::new('email')
            ->hideOnIndex()
        ;


        yield FormField::addPanel('Security');
        yield BooleanField::new('enabled');
        yield ChoiceField::new('roles')
            ->setChoices([
                'Usuario' => 'ROLE_USER',
                'Gestor' => 'ROLE_ADMIN',
                'Administrador' => 'ROLE_SUPER_ADMIN',
            ])
            ->allowMultipleChoices()
        ;
        yield AssociationField::new('versions')
            ->setLabel('Policies')
            ->setTemplatePath('admin/user/policies.html.twig')
            ->onlyOnDetail();

        yield FormField::addPanel('Profile');
        yield IdField::new('id')
            ->onlyOnIndex()
        ;
        yield TextField::new('firstname');
        yield TextField::new('lastname');
        yield ChoiceField::new('collective')
            ->setChoices(User::getCollectives())
        ;
        yield TextField::new('nic')
            ->hideOnIndex()
        ;
        yield AssociationField::new('degree')
            ->hideOnIndex()
        ;
        yield TextField::new('year')
            ->hideOnIndex()
        ;
        yield TextareaField::new('imageFile')
            ->setFormType(VichImageType::class)
            ->onlyOnForms()
        ;
        yield ImageField::new('image.name')
            ->setBasePath('/images/avatars')
            ->setCssClass('ea-vich-image')
            ->onlyOnDetail()
        ;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}

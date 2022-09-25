<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Group;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class GroupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Group::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Groups')
            ->setEntityLabelInSingular('Group');
    }

    /** @inheritDoc */
    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('General');
        yield IdField::new('id')
            ->onlyOnIndex();

        yield TextField::new('name');
        yield TextareaField::new('description')->setRequired(true);
        yield TextField::new('icon');
        yield TextField::new('slug')
            ->onlyOnDetail();

        yield TextareaField::new('imageFile')
            ->setFormType(VichImageType::class)
            ->onlyOnForms();

        yield ImageField::new('image.name')
            ->setBasePath('/images/groups')
            ->setCssClass('ea-vich-image')
            ->onlyOnDetail();

        yield AssociationField::new('users')
            ->setFormTypeOptionIfNotSet('by_reference', false);
    }
}

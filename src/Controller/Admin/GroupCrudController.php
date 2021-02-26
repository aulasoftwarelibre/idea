<?php

namespace App\Controller\Admin;

use App\Entity\Group;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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
            ->setEntityLabelInSingular('Group')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('General');
        yield IdField::new('id')
            ->onlyOnIndex()
        ;
        yield TextField::new('name');
        yield TextField::new('icon');
        yield TextField::new('slug')
            ->onlyOnDetail()
        ;
    }
}

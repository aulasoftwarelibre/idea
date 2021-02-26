<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Degree;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DegreeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Degree::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Degrees')
            ->setEntityLabelInSingular('Degree');
    }

    /**
     * @inheritDoc
     */
    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('General');
        yield IdField::new('id')
            ->onlyOnIndex();

        yield TextField::new('name');
        yield TextField::new('slug')
            ->onlyOnDetail();
    }
}

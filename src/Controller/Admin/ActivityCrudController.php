<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Activity;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ActivityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Activity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Ideas')
            ->setEntityLabelInSingular('Idea')
            ->setDefaultSort(['occurredOn' => 'DESC']);
    }

    /** @inheritDoc */
    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('General');
        yield IdField::new('id')
            ->hideOnForm();

        yield TextField::new('title');
        yield TextField::new('academicYear');
        yield DateField::new('occurredOn');
        yield NumberField::new('duration');
        yield DateField::new('createdAt')
            ->onlyOnDetail();

        yield FormField::addPanel('Users');
        yield AssociationField::new('participations')
            ->setTemplatePath('/admin/activity/inscriptions.html.twig')
            ->onlyOnDetail();
    }
}

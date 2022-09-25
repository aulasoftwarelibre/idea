<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Participation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\HttpFoundation\Request;

use function assert;

class ParticipationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Participation::class;
    }

    /** @inheritDoc */
    public function createEntity(string $entityFqcn)
    {
        $entity = new Participation();
        $entity->setRole(Participation::ATTENDEE);

        $request = $this->container->get('request_stack')->getCurrentRequest();
        assert($request instanceof Request);
        $request->query->get('filters');

        return $entity;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Inscriptions')
            ->setEntityLabelInSingular('Inscription')
            ->setSearchFields(['user.firstname', 'user.lastname', 'user.email', 'activity.title'])
            ->setDefaultSort([
                'activity.title' => 'ASC',
                'user.lastname' => 'ASC',
            ]);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('activity')->setFormTypeOption('attr.data-widget', 'select2'))
            ->add('user');
    }

    /** @inheritDoc */
    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('General');
        yield IdField::new('id')
            ->hideOnForm();

        yield AssociationField::new('user')
            ->setSortable(false);

        yield TextField::new('user.email', 'email')
            ->setSortable(false)
            ->hideOnForm();

        yield AssociationField::new('activity')
            ->setSortable(false);

        yield ChoiceField::new('role')
            ->setChoices(Participation::getRoles());
    }
}

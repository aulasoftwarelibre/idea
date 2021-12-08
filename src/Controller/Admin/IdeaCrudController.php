<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\Field\VoteField;
use App\Entity\Idea;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class IdeaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Idea::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $linkToIdea = Action::new('linkAction', 'Abrir', 'fa fa-link')
            ->linkToRoute('idea_show', static function (Idea $idea) {
                return [
                    'slug' => $idea->getSlug(),
                ];
            });

        return $actions
            ->add(Crud::PAGE_EDIT, $linkToIdea)
            ->add(Crud::PAGE_DETAIL, $linkToIdea);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Ideas')
            ->setEntityLabelInSingular('Idea')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }

    /**
     * @inheritDoc
     */
    public function configureFields(string $pageName): iterable
    {
        if ($pageName === Crud::PAGE_INDEX) {
            yield IdField::new('id');
            yield TextField::new('title');
            yield BooleanField::new('highlight')
                ->renderAsSwitch(true);

            yield BooleanField::new('closed')
                ->renderAsSwitch(true);

            yield ChoiceField::new('format')
                ->setChoices(Idea::getFormats());

            yield DateField::new('createdAt');

            return;
        }

        yield FormField::addPanel('block.content');
        yield TextField::new('title');
        yield TextareaField::new('description')
            ->setFormType(CKEditorType::class)
            ->setTemplatePath('/admin/idea/description.html.twig');

        yield TextareaField::new('imageFile')
            ->setFormType(VichImageType::class)
            ->onlyOnForms();

        yield ImageField::new('image.name')
            ->setBasePath('/images/ideas')
            ->setCssClass('ea-vich-image')
            ->onlyOnDetail();

        yield FormField::addPanel('block.state');
        yield BooleanField::new('closed');
        yield BooleanField::new('private');
        yield BooleanField::new('highlight');
        yield ChoiceField::new('state')
            ->setChoices(Idea::getStates())
            ->setTemplatePath('/admin/idea/state.html.twig');

        yield AssociationField::new('owner');

        yield FormField::addPanel('block.seats');
        yield BooleanField::new('internal')
            ->setHelp('Activa esta casilla para actividades exclusivas UCO');

        yield NumberField::new('numSeats')
            ->setHelp('Número de plazas de la actividad. Pon 0 para ilimitadas.');

        yield NumberField::new('externalNumSeats')
            ->setHelp('Límite de plazas externas. Pon 0 para ilimitadas (o hasta llenar el límite');

        yield DateTimeField::new('startsAt');
        yield DateTimeField::new('endsAt');

        yield FormField::addPanel('block.format');
        yield ChoiceField::new('format')
            ->setChoices(Idea::getFormats())
            ->setTemplatePath('/admin/idea/state.html.twig');

        yield TextField::new('location')
            ->setHelp('form.help_location');

        yield BooleanField::new('isJitsiRoomOpen')
            ->setHelp('form.help_jitsi_room_open');

        yield TextField::new('jitsiLocatorRoom')
            ->setHelp('form.help_jitsi_locator_room')
            ->hideOnForm();

        yield FormField::addPanel('block.votes');
        yield VoteField::new('votes');
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of the `idea` project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin;

use App\Entity\Idea;
use App\Form\Type\SonataVoteType;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DateTimePickerType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use function in_array;

class IdeaAdmin extends AbstractAdmin
{
    /**
     * @var array<string, mixed>
     * @inheritdoc
     */
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    ];

    public function getNewInstance(): ?Idea
    {
        return null;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('block.content', ['class' => 'col-md-12'])
                ->add('title', null, [])
                ->add('description', SimpleFormatterType::class, [
                    'format' => 'richhtml',
                    'ckeditor_context' => 'simple_toolbar',
                    'attr' => ['rows' => 20],
                ])
                ->add('group', null, ['placeholder' => 'Seleccione un grupo'])
            ->end()
            ->with('block.state', ['class' => 'col-md-6'])
                ->add('closed', null, [])
                ->add('private', null, [])
                ->add('state', ChoiceType::class, [
                    'choices' => Idea::getStates(),
                ])
                ->add('owner', null, ['placeholder' => 'Seleccione un usuario'])
            ->end()
            ->with('block.seats', ['class' => 'col-md-6'])
                ->add('internal', null, ['help' => 'Activa esta casilla para actividades exclusivas UCO'])
                ->add('numSeats', null, ['help' => 'Número de plazas de la actividad. Pon 0 para ilimitadas.'])
                ->add('externalNumSeats', null, ['help' => 'Límite de plazas externas. Pon 0 para ilimitadas (o hasta llenar el límite)'])
            ->end()
            ->with('block.location', ['class' => 'col-md-6'])
                ->add('location', null, [
                    'required' => false,
                    'help' => 'form.help_location',
                ])
                ->add('startsAt', DateTimePickerType::class, [
                    'format' => 'd/M/y HH:mm',
                    'required' => false,
                ])
                ->add('endsAt', DateTimePickerType::class, [
                    'format' => 'd/M/y HH:mm',
                    'required' => false,
                ])
            ->end()
            ->with('block.online', ['class' => 'col-md-6'])
                ->add('isOnline', null, ['required' => false])
                ->add('jitsiLocatorRoom', null, [
                    'required' => false,
                    'help' => 'form.help_jitsi_locator_room',
                    'disabled' => true,
                ])
                ->add('isJitsiRoomOpen', null, ['required' => false])
            ->end()
            ->with('block.votes', ['class' => 'col-md-12'])
                ->add('votes', SonataVoteType::class, [
                    'multiple' => true,
                    'required' => false,
                ])
            ->end();
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('title', null, [
                'route' => ['name' => 'show'],
            ])
            ->add('closed', null, [])
            ->add('state', null, ['template' => '/backend/Idea/list_field_state.html.twig'])
            ->add('createdAt', null, ['locale' => 'es'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                ],
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('title', null, [])
            ->add('owner', null, [])
            ->add('group', null, [])
            ->add('state', 'doctrine_orm_choice', [], ChoiceType::class, [
                'choices' => Idea::getStates(),
            ])
            ->add('closed');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('title', null, [])
            ->add('description', null, ['safe' => true])
            ->add('owner', null, [
                'route' => ['name' => 'show'],
            ])
            ->add('group', null, [
                'route' => ['name' => 'show'],
            ])
            ->add('closed', null, [])
            ->add('state', null, [])
            ->add('createdAt', null, [])
            ->add('updatedAt', null, []);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureBatchActions($actions)
    {
        if ($this->hasAccess('edit')) {
            $actions['open']  = [];
            $actions['close'] = [];
        }

        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, ?AdminInterface $childAdmin = null): void
    {
        if (! in_array($action, ['edit', 'show'], true)) {
            return;
        }

        $id     = $this->getRequest()->get('id');
        $object = $this->getObject($id);

        $menu->addChild('Ver en el portal', [
            'uri' => $this->getConfigurationPool()->getContainer()->get('router')->generate('idea_show', ['slug' => $object->getSlug()]),
        ]);
    }
}

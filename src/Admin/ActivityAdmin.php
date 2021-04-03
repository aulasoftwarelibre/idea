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

use App\Entity\Activity;
use App\Form\DataMapper\GenericDataMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DatePickerType;

use function assert;
use function in_array;

class ActivityAdmin extends AbstractAdmin
{
    /**
     * @var array<string, mixed>
     * @inheritdoc
     */
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'occurredOn',
    ];

    public function getNewInstance(): ?Activity
    {
        return null;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('title', null, [])
            ->add('academicYear', null, [])
            ->add('occurredOn', DatePickerType::class, ['format' => 'd/M/y'])
            ->add('duration', null, []);

        $form
            ->getFormBuilder()
            ->setEmptyData(null)
            ->setDataMapper(new GenericDataMapper(Activity::class));
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('title', null, [
                'route' => ['name' => 'show'],
            ])
            ->add('occurredOn', null, ['format' => 'd/M/y'])
            ->add('duration', null, [])
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
            ->add('title', null, ['show_filter' => true]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('Activities')
                ->add('title', null, [])
                ->add('occurredOn', null, ['format' => 'd/M/y'])
                ->add('duration', null, [])
                ->add('createdAt', null, [])
                ->add('updatedAt', null, [])
            ->end()
            ->with('Users')
                ->add('participations', null, ['template' => '/backend/Activity/show_participation.html.twig'])
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, ?AdminInterface $childAdmin = null): void
    {
        if (! $childAdmin && ! in_array($action, ['edit', 'show'], true)) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        assert($admin instanceof AdminInterface);
        $id = $admin->getRequest()->get('id');

        if (! $this->isGranted('LIST')) {
            return;
        }

        $menu->addChild('Manage Participations', [
            'uri' => $admin->generateUrl('eco.admin.participation.list', ['id' => $id]),
        ]);
    }
}

<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\DatePickerType;

class ActivityAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('title', null, [
            ])
            ->add('academicYear', null, [
            ])
            ->add('occurredOn', DatePickerType::class, [
                'format' => 'd/M/y',
            ])
            ->add('duration', null, [
            ])
        ;
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('title', null, [
                'route' => ['name' => 'show'],
            ])
            ->add('occurredOn', null, [
                'format' => 'd/M/y',
            ])
            ->add('duration', null, [
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                ],
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('title', null, [
                'show_filter' => true,
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('title', null, [
            ])
            ->add('occurredOn', null, [
                'format' => 'd/M/y',
            ])
            ->add('duration', null, [
            ])
            ->add('createdAt', null, [
            ])
            ->add('updatedAt', null, [
            ])
        ;
    }

    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, ['edit', 'show'], true)) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        $id = $admin->getRequest()->get('id');

        if ($this->isGranted('LIST')) {
            $menu->addChild('Manage Participations', [
                'uri' => $admin->generateUrl('eco.admin.participation.list', ['id' => $id]),
            ]);
        }
    }
}

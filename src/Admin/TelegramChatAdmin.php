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

use App\Entity\TelegramChat;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TelegramChatAdmin extends AbstractAdmin
{
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id', null, [
                'route' => ['name' => 'show'],
            ])
            ->add('type', null, [
                'template' => 'backend/TelegramChat/list_field_title.html.twig',
            ])
            ->add('title', null, [
            ])
            ->add('active', null, [
                'editable' => true,
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                ],
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('id')
            ->add('type', null, [], ChoiceType::class, [
                'choices' => [
                    'Channel' => TelegramChat::CHANNEL,
                    'Group' => TelegramChat::GROUP,
                    'Supergroup' => TelegramChat::SUPERGROUP,
                    'Private' => TelegramChat::PRIVATE,
                ],
            ])
            ->add('title')
            ->add('active')
        ;
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('id')
            ->add('type', null, [
                'template' => 'backend/TelegramChat/show_field_title.html.twig',
            ])
            ->add('title')
            ->add('username')
            ->add('active')
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('edit');
        $collection->remove('create');
    }
}

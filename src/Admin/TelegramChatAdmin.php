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

use App\Entity\TelegramChat;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TelegramChatAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('title', null, [
                'disabled' => true,
            ])
            ->add('active', null, [
                'required' => false,
            ])
            ->add('welcomeMessage', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'simple_toolbar',
                'attr' => ['rows' => 20],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, [
                'route' => ['name' => 'show'],
            ])
            ->add('type', null, [
                'template' => 'backend/TelegramChat/list_field_title.html.twig',
            ])
            ->add('title', null, [
                'template' => '/backend/TelegramChat/type_field_list.html.twig',
                'label' => 'list.label_name',
            ])
            ->add('user.username', null, [
                'label' => 'list.label_user',
            ])
            ->add('active', null, [
                'editable' => true,
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'show' => [],
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
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
            ->add('active');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('type', null, [
                'template' => 'backend/TelegramChat/show_field_title.html.twig',
            ])
            ->add('title')
            ->add('username')
            ->add('active')
            ->add('welcomeMessage')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->remove('create');
    }
}

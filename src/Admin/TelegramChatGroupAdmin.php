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
use App\Entity\TelegramChatGroup;
use App\Form\DataMapper\GenericDataMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;

class TelegramChatGroupAdmin extends AbstractAdmin
{
    public function getNewInstance(): ?TelegramChat
    {
        return null;
    }

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

        $form
            ->getFormBuilder()
            ->setEmptyData(null)
            ->setDataMapper(new GenericDataMapper(TelegramChatGroup::class));
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
            ->add('title', null, [
            ])
            ->add('active', null, [
                'editable' => true,
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'show' => [],
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('title')
            ->add('active')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('title')
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

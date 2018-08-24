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
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class IdeaAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    ];

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('title', null, [
            ])
            ->add('description', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'simple_toolbar',
                'attr' => ['rows' => 20],
            ])
            ->add('closed', null, [
            ])
            ->add('private', null, [
            ])
            ->add('state', ChoiceType::class, [
                'choices' => Idea::getStates(),
            ])
            ->add('owner', null, [
                'placeholder' => 'Seleccione un usuario',
            ])
            ->add('group', null, [
                'placeholder' => 'Seleccione un grupo',
            ])
            ->add('numSeats', null, [
            ])
            ->add('votes', SonataVoteType::class, [
                'multiple' => true,
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('title', null, [
                'route' => ['name' => 'show'],
            ])
            ->add('closed', null, [
            ])
            ->add('state', null, [
            ])
            ->add('createdAt', null, [
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('title', null, [
            ])
            ->add('owner', null, [
            ])
            ->add('group', null, [
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('title', null, [
            ])
            ->add('description', null, [
                'safe' => true,
            ])
            ->add('owner', null, [
                'route' => ['name' => 'show'],
            ])
            ->add('group', null, [
                'route' => ['name' => 'show'],
            ])
            ->add('closed', null, [
            ])
            ->add('state', null, [
            ])
            ->add('createdAt', null, [
            ])
            ->add('updatedAt', null, [
            ]);
    }
}

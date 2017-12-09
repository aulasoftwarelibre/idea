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

use App\Entity\User;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\UserBundle\Admin\Model\UserAdmin as BaseUserAdmin;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserAdmin extends BaseUserAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        parent::configureFormFields($formMapper);

        $formMapper->remove('dateOfBirth');
        $formMapper->remove('website');
        $formMapper->remove('biography');
        $formMapper->remove('gender');
        $formMapper->remove('locale');
        $formMapper->remove('timezone');

        $formMapper->remove('facebookUid');
        $formMapper->remove('facebookName');
        $formMapper->remove('twitterUid');
        $formMapper->remove('twitterName');
        $formMapper->remove('gplusUid');
        $formMapper->remove('gplusName');

        $formMapper->remove('token');
        $formMapper->remove('twoStepVerificationCode');

        $formMapper
            ->tab('User')
                ->with('Profile')
                    ->add('collective', ChoiceType::class, [
                        'choices' => User::getCollectives(),
                    ])
                    ->add('nic')
                    ->add('degree')
                    ->add('year')
                ->end()
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        parent::configureListFields($listMapper);

        $listMapper
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper): void
    {
        parent::configureDatagridFilters($filterMapper);

        $filterMapper
            ->add('firstname', null, [
                'show_filter' => true,
            ])
            ->add('lastname', null, [
                'show_filter' => true,
            ])
            ->add('nic', null, [
                'show_filter' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->with('General')
                ->add('username')
                ->add('email')
            ->end()
            ->with('Groups')
                ->add('groups')
            ->end()
            ->with('Profile')
                ->add('firstname')
                ->add('lastname')
                ->add('nic')
                ->add('collective')
                ->add('degree')
                ->add('year')
            ->end()
        ;
    }
}

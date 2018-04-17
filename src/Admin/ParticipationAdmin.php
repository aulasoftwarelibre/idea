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

use App\Entity\Participation;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ParticipationAdmin extends AbstractAdmin
{
    protected $parentAssociationMapping = 'activity';

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('user', ModelAutocompleteType::class, [
                'property' => ['firstname', 'lastname', 'username'],
            ])
            ->add('role', ChoiceType::class, [
                'choices' => Participation::getRoles(),
            ])
            ->add('isReported', null, [
            ])
        ;
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('user')
            ->add('user.nic')
            ->add('user.email')
            ->add('role', ChoiceType::class, [
                'choices' => Participation::getRoles(),
            ])
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
    public function toString($object)
    {
        return $object instanceof Participation
            ? $object->getUser()->getUsername()
            : 'Participante'; // shown in the breadcrumb on the create view
    }

    public function getExportFields()
    {
        return [
            'user.firstname',
            'user.lastname',
            'user.nic',
            'role',
        ];
    }
}

<?php


namespace App\Admin;


use Sonata\AdminBundle\Form\FormMapper;
use Sonata\UserBundle\Admin\Model\GroupAdmin as BaseGroupAdmin;
use Sonata\UserBundle\Form\Type\SecurityRolesType;

final class GroupAdmin extends BaseGroupAdmin
{
    /**
     * @inheritDoc
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        parent::configureFormFields($formMapper);

        $formMapper
            ->tab('Group')
                ->with('General', ['class' => 'col-md-6'])
                    ->add('icon')
                ->end()
            ->end()
        ;
    }

}

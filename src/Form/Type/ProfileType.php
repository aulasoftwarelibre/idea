<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Type;

use App\Entity\Degree;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', null, [
                'required' => true,
            ])
            ->add('lastname', null, [
                'required' => true,
            ])
            ->add('biography', TextareaType::class, [
                'required' => false,
            ])
            ->add('collective', ChoiceType::class, [
                'required' => true,
                'placeholder' => 'Selecciona tu colectivo',
                'choices' => User::getCollectives(),
            ])
            ->add('degree', EntityType::class, [
                'class' => Degree::class,
                'placeholder' => 'Selecciona tus estudios',
                'attr' => [
                    'class' => 'ui search dropdown',
                ],
            ])
            ->add('area', null, [
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => User::class,
            ])
        ;
    }
}

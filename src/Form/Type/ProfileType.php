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
use App\Entity\TelegramChat;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', null, [
                'label' => 'Nombre',
                'required' => true,
            ])
            ->add('lastname', null, [
                'label' => 'Apellidos',
                'required' => true,
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Imagen de perfil',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => '¿Borrar?',
                'download_label' => 'Descargar',
                'download_uri' => false,
                'image_uri' => false,
                'imagine_pattern' => 'squared_thumbnail',
            ])
            ->add('biography', TextareaType::class, [
                'label' => 'Biografía',
                'required' => false,
            ])
            ->add('collective', ChoiceType::class, [
                'label' => 'Colectivo',
                'required' => true,
                'placeholder' => 'Selecciona tu colectivo',
                'choices' => User::getCollectives(),
            ])
            ->add('degree', EntityType::class, [
                'label' => 'Estudios',
                'required' => false,
                'class' => Degree::class,
                'placeholder' => 'Selecciona tus estudios',
                'attr' => [
                    'class' => 'ui search dropdown',
                ],
            ])
            ->add('year', null, [
                'label' => 'Año de ingreso',
            ])
        ;

        /** @var User $profile */
        $profile = $builder->getData();

        if ($profile->getTelegramChat()) {
            $builder
                ->add('telegramChat', ProfileTelegramOptionsType::class, [
                ])
            ;
        }
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

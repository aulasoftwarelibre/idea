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

namespace App\Form\Type;

use App\Entity\Degree;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Vich\UploaderBundle\Form\Type\VichImageType;

use function assert;

class RegisterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $profile = $builder->getData();
        assert($profile instanceof User);

        $builder
            ->add('alias', null, [
                'label' => 'Alias',
                'required' => true,
                'help' => 'form.help_alias',
            ])
            ->add('firstname', null, [
                'label' => 'Nombre',
                'required' => true,
            ])
            ->add('lastname', null, [
                'label' => 'Apellidos',
                'required' => true,
            ])
            ->add('nic', null, [
                'label' => 'DNI/NIE',
                'required' => false,
                'help' => 'form.help_nic',
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
            ->add('collective', ChoiceType::class, [
                'label' => 'Colectivo',
                'required' => true,
                'placeholder' => 'Selecciona tu colectivo',
                'choices' => User::getCollectives(),
                'disabled' => $profile->isExternal(),
            ])
            ->add('degree', EntityType::class, [
                'label' => 'Estudios',
                'required' => false,
                'class' => Degree::class,
                'placeholder' => 'Selecciona tus estudios',
                'attr' => ['class' => 'ui search dropdown'],
            ])
            ->add('year', null, ['label' => 'Año de ingreso'])
            ->add('terms', CheckboxType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new IsTrue(['message' => 'Debe aceptar los términos de uso']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => User::class,
            ]);
    }
}

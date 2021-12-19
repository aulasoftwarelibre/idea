<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class GroupType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'maxlength' => 200,
                    'rows' => 3,
                    'autofocus' => true,
                ],
                'help' => 'Máximo 200 caracteres.',
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Imagen de perfil (relación 2:1, min. 1200x600)',
                'required' => true,
                'allow_delete' => false,
                'delete_label' => '¿Borrar?',
                'download_label' => 'Descargar',
                'download_uri' => false,
                'image_uri' => false,
                'imagine_pattern' => 'opengraph_thumbnail',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => Group::class,
            ]);
    }
}

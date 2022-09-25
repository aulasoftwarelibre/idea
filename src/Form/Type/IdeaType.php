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

use App\Entity\Group;
use App\Entity\Idea;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Vich\UploaderBundle\Form\Type\VichImageType;

class IdeaType extends AbstractType
{
    public function __construct(
        private Security $security,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, ['required' => true])
            ->add('description', CKEditorType::class, [
                'required' => true,
                'purify_html' => true,
                'attr' => ['rows' => 20],
            ])
            ->add('group', EntityType::class, [
                'class' => Group::class,
                'placeholder' => 'Seleccione un grupo donde publicar la idea',
                'required' => true,
            ])
            ->add('imageFile', VichImageType::class, ['required' => false])
            ->add('startsAt', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy, HH:mm',
                'html5' => false,
                'label' => 'Starts At',
            ])
            ->add('endsAt', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy, HH:mm',
                'html5' => false,
                'label' => 'Ends At',
            ])
            ->add('location', null, ['required' => false])
            ->add('numSeats', null, [
                'required' => false,
                'label' => 'Num Seats',
                'help' => '0 para plazas ilimitadas',
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $form = $event->getForm();
            $data = $event->getData();

            $isNew    = $data?->getId() === null;
            $isMember = $this->security->isGranted('GROUP_MEMBER', $data?->getGroup());

            if (! $isNew) {
                unset($form['group']);
            }

            if ($isMember) {
                return;
            }

            unset($form['startsAt']);
            unset($form['endsAt']);
            unset($form['location']);
            unset($form['imageFile']);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => Idea::class,
            ]);
    }
}

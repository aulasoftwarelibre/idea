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
use App\Entity\User;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class IdeaType extends AbstractType implements DataMapperInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $token;

    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'form.label_title',
                'required' => true,
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'form.label_description',
                'required' => true,
                'purify_html' => true,
                'attr' => ['rows' => 20],
            ])
            ->add('group', EntityType::class, [
                'label' => 'form.label_group',
                'class' => Group::class,
                'placeholder' => 'Seleccione un grupo donde publicar la idea',
                'required' => true,
            ])
            ->setDataMapper($this)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function mapDataToForms($data, $forms): void
    {
        $forms = iterator_to_array($forms);

        $forms['title']->setData($data ? $data->getTitle() : '');
        $forms['description']->setData($data ? $data->getDescription() : '');
        $forms['group']->setData($data ? $data->getGroup() : null);
    }

    /**
     * {@inheritdoc}
     */
    public function mapFormsToData($forms, &$data): void
    {
        $forms = iterator_to_array($forms);

        $title = $forms['title']->getData();
        $description = $forms['description']->getData();
        $group = $forms['group']->getData();

        $user = null !== $this->token->getToken() ? $this->token->getToken()->getUser() : null;
        if (!$user instanceof User) {
            throw new \RuntimeException('User not logged');
        }

        if (!$data instanceof Idea) {
            $data = new Idea(
                $title,
                $description,
                $user,
                $group
            );
        } else {
            $data
                ->setTitle($title)
                ->setDescription($description)
                ->setGroup($group)
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => Idea::class,
                'empty_data' => null,
            ])
        ;
    }
}

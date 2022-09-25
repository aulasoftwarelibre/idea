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

use App\Form\DataTransformer\ArrayToVoteTransform;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoteType extends AbstractType
{
    public function __construct(private UserRepository $repository)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new ArrayToVoteTransform($this->repository));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => $this->repository->getChoices(),
            ]);
    }

    public function getParent(): string|null
    {
        return ChoiceType::class;
    }
}

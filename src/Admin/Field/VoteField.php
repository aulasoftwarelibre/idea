<?php

declare(strict_types=1);

namespace App\Admin\Field;

use App\Form\Type\VoteType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

final class VoteField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): VoteField
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath('admin/idea/votes.html.twig')
            ->setFormType(VoteType::class)
            ->setFormTypeOption('multiple', true)
            ->setFormTypeOption('attr.data-widget', 'select2');
    }
}

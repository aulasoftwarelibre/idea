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

namespace App\Validator;

use Attribute;
use Doctrine\Common\Annotations\Annotation\Target;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "CLASS", "ANNOTATION"})
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class Alias extends Constraint
{
    /** @var string message */
    public string $message = 'El alias ya está reservado por otro usuario.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

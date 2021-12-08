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

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AliasValidator extends ConstraintValidator
{
    private UserRepository $userRepository;

    /**
     * {@inheritdoc}
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($value === null || $value === '' || $constraint instanceof Alias) {
            return;
        }

        if (! $value instanceof User) {
            throw new UnexpectedTypeException($value, User::class);
        }

        $user = $this->userRepository->findUsedAliasOrUsername($value->getAlias());
        if ((! $user instanceof User) || $user->getId() === $value->getId()) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}

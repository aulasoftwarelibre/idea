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
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * {@inheritdoc}
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param User|string|null $entity
     * @param Alias            $constraint
     */
    public function validate($entity, Constraint $constraint): void
    {
        if (null === $entity || '' === $entity) {
            return;
        }

        if (!$entity instanceof User) {
            throw new UnexpectedTypeException($entity, User::class);
        }

        $user = $this->userRepository->findUsedAliasOrUsername($entity->getAlias());
        if ((!$user instanceof User) || $user->getId() === $entity->getId()) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}

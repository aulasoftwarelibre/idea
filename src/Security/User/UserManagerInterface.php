<?php

declare(strict_types=1);

namespace App\Security\User;

use App\Entity\User;

interface UserManagerInterface
{
    /** @param array<array-key, mixed> $criteria */
    public function findUserBy(array $criteria): User|null;

    public function updateUser(User $user): void;

    public function deleteUser(User $user): void;
}

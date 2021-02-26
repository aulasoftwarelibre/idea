<?php


namespace App\Security\User;


use App\Entity\User;

interface UserManagerInterface
{

    public function findUserBy(array $criteria);

    public function updateUser(User $user);

    public function deleteUser(User $user);
}

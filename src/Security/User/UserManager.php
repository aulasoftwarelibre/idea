<?php

declare(strict_types=1);

namespace App\Security\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserManager implements UserManagerInterface
{
    private UserRepository $userRepository;
    private EntityManagerInterface $manager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $manager)
    {
        $this->userRepository = $userRepository;
        $this->manager        = $manager;
    }

    /**
     * @inheritDoc
     */
    public function findUserBy(array $criteria): ?User
    {
        return $this->userRepository->findOneBy($criteria);
    }

    public function updateUser(User $user): void
    {
        $this->manager->persist($user);
        $this->manager->flush();
    }

    public function deleteUser(User $user): void
    {
        $this->manager->remove($user);
        $this->manager->flush();
    }
}

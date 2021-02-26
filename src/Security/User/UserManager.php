<?php


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
        $this->manager = $manager;
    }

    public function findUserBy(array $criteria)
    {
        return $this->userRepository->findOneBy($criteria);
    }

    public function updateUser(User $user)
    {
        $this->manager->persist($user);
        $this->manager->flush();
    }

    public function deleteUser(User $user)
    {
        $this->manager->remove($user);
        $this->manager->flush();
    }
}

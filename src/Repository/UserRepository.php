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

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $user): void
    {
        $this->_em->persist($user);
    }

    public function remove(User $user): void
    {
        $this->_em->remove($user);
    }

    public function getChoices(): array
    {
        $users = $this->createQueryBuilder('o')
            ->select('o.id as id, o.username as username, o.firstname as firstname, o.lastname as lastname')
            ->getQuery()
            ->getArrayResult();

        $result = [];
        foreach ($users as $user) {
            $key = "{$user['firstname']} {$user['lastname']} - {$user['username']}";
            $result[$key] = $user['id'];
        }

        return $result;
    }

    public function getProfile(int $userId): ?User
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT o, g, d, p, a
                FROM App:User o
                LEFT JOIN o.groups g
                LEFT JOIN o.degree d
                LEFT JOIN o.participations p
                LEFT JOIN p.activity a
                WHERE o.id = :id
                ORDER BY a.occurredOn DESC
            ')
            ->setParameter('id', $userId)
            ->getOneOrNullResult()
            ;
    }

    public function findUsedAliasOrUsername(string $find): ?User
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT u
                FROM App:User u
                WHERE u.username = :username
                   OR u.alias = :alias
            ')
            ->setParameter('username', $find)
            ->setParameter('alias', $find)
            ->getOneOrNullResult()
        ;
    }

    public function findAllDeletedUsers(): array
    {
        $qb = $this->getEntityManager()
            ->createQuery('
                SELECT u 
                FROM App\Entity\User u 
                WHERE u.deletedAt IS NOT NULL
                AND u.deletedAt < :oneYearAgo
            ')
            ->setParameter('oneYearAgo', new \DateTime('1 year ago'))
        ;

        return $qb->getResult();
    }
}

<?php

/*
 * This file is part of the `idea` project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

class UserRepository extends CeoRepository
{
    public function getChoices()
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

    public function getProfile(int $userId)
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

    public function findOneByValidToken(string $token)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT u
                FROM App:User u
                WHERE u.telegramSecretToken = :token
                      AND u.telegramSecretTokenExpiresAt > :now
            ')
            ->setParameter('token', $token)
            ->setParameter('now', new \DateTime())
            ->getOneOrNullResult()
            ;
    }
}

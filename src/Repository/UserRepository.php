<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

class UserRepository extends CeoRepository
{
    public function getProfile(int $userId)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT o, g, d
                FROM App:User o
                LEFT JOIN o.groups g
                LEFT JOIN o.degree d
                WHERE o.id = :id
            ')
            ->setParameter('id', $userId)
            ->getOneOrNullResult()
            ;
    }
}

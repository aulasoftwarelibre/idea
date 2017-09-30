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

use App\Entity\Group;
use Pagerfanta\Pagerfanta;

class IdeaRepository extends CeoRepository
{
    /**
     * @param int $page
     *
     * @return Pagerfanta
     */
    public function findLatest(int $page = 1): Pagerfanta
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT i, v, u, g, o
                FROM App:Idea i
                LEFT JOIN i.votes v
                LEFT JOIN v.user u
                LEFT JOIN i.group g
                LEFT JOIN i.owner o
                ORDER BY i.createdAt DESC
            ');

        return $this->createPaginator($query, $page);
    }

    /**
     * @param Group $group
     * @param int   $page
     *
     * @return Pagerfanta
     */
    public function findByGroup(Group $group, int $page = 1): Pagerfanta
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT i, v, u, g, o
                FROM App:Idea i
                LEFT JOIN i.votes v
                LEFT JOIN v.user u
                LEFT JOIN i.group g
                LEFT JOIN i.owner o
                WHERE i.group = :groupId
                ORDER BY i.createdAt DESC'
        )->setParameter('groupId', $group);

        return $this->createPaginator($query, $page);
    }
}

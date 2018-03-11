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
use App\Entity\Idea;
use Pagerfanta\Pagerfanta;

class IdeaRepository extends CeoRepository
{
    /**
     * @param int $page
     *
     * @return Pagerfanta
     */
    public function findLatest(int $page, bool $showPrivates): Pagerfanta
    {
        $qb = $this->createQueryBuilder('i')
            ->leftJoin('i.group', 'g')
            ->leftJoin('i.owner', 'o')
            ->orderBy('i.createdAt', 'DESC');

        if (false === $showPrivates) {
            $qb->andWhere('i.private = :false')
                ->setParameter('false', false);
        }

        $query = $qb->getQuery();

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
                SELECT i, g, o
                FROM App:Idea i
                LEFT JOIN i.group g
                LEFT JOIN i.owner o
                WHERE i.group = :groupId
                ORDER BY i.createdAt DESC'
        )->setParameter('groupId', $group);

        return $this->createPaginator($query, $page);
    }
}

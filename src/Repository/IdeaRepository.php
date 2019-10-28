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

use App\Entity\Group;
use App\Entity\Idea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class IdeaRepository extends ServiceEntityRepository
{
    public const NUM_ITEMS = 5;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Idea::class);
    }

    public function add(Idea $user): void
    {
        $this->_em->persist($user);
    }

    public function remove(Idea $user): void
    {
        $this->_em->remove($user);
    }

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
            ->createQuery(
                '
                SELECT i, g, o
                FROM App:Idea i
                LEFT JOIN i.group g
                LEFT JOIN i.owner o
                WHERE i.group = :groupId
                ORDER BY i.createdAt DESC'
            )->setParameter('groupId', $group);

        return $this->createPaginator($query, $page);
    }

    public function findFilteredByVotes(): array
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT i, COUNT(v.id) as votes
                FROM App:Idea i
                JOIN i.votes v
                WHERE i.state = :status
                AND i.closed = FALSE
                GROUP BY i.id
                ORDER BY COUNT (v.id) DESC 
            ')
            ->setParameter('status', Idea::STATE_PROPOSED)
            ->setMaxResults(5)
            ->execute();
    }

    public function findNextScheduled(): array
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT i
                FROM App:Idea i
                WHERE i.startsAt IS NOT NULL
                AND i.startsAt > :now
                AND i.state = :approved
            ')
            ->setParameter('now', new \DateTime())
            ->setParameter('approved', Idea::STATE_APPROVED)
            ->setMaxResults(5)
            ->execute();
    }

    /**
     * @param Query $query
     * @param int   $page
     *
     * @return Pagerfanta
     */
    protected function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query, false));
        $paginator->setMaxPerPage(self::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}

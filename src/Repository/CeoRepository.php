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

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

abstract class CeoRepository extends EntityRepository
{
    const NUM_ITEMS = 10;

    /**
     * @param $object
     */
    public function add($object)
    {
        $this->_em->persist($object);
    }

    /**
     * @param $object
     */
    public function remove($object)
    {
        $this->_em->remove($object);
    }

    /**
     * @param Query $query
     * @param int   $page
     *
     * @return Pagerfanta
     */
    protected function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(self::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}

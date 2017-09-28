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

use Pagerfanta\Pagerfanta;

class IdeaRepository extends CeoRepository
{
    public function findLatest(int $page = 1): Pagerfanta
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT i
                FROM App:Idea i
                ORDER BY i.createdAt DESC
            ')
        ;

        return $this->createPaginator($query, $page);
    }
}

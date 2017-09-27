<?php
/**
 * This file is part of the ceo.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 * (c) Sergio Gómez <sergio@uco.es>
 * (c) Omar Sotillo <i32sofro@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

abstract class CeoRepository extends EntityRepository
{
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
}

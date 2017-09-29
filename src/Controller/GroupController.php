<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GroupController extends Controller
{
    public function listGroupsAction(GroupRepository $repository)
    {
        $groups = $repository->findBy([], ['name' => 'ASC']);

        return $this->render('frontend/group/_groups.twig', [
            'groups' => $groups, ]);
    }
}

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

use App\Entity\Group;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GroupController extends Controller
{
    public function listGroupsAction()
    {
        $groups = $this->getDoctrine()->getRepository(Group::class)->findAll();

        return $this->render('frontend/group/listGroups.html.twig', [
            'groups' => $groups, ]);
    }
}

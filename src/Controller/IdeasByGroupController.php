<?php
/**
 * Created by PhpStorm.
 * User: omarsotillo
 * Date: 29/09/17
 * Time: 13:20
 */

namespace App\Controller;

use App\Command\GetIdeasByGroupQuery;
use App\Entity\Group;
use League\Tactician\CommandBus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class IdeasByGroupController extends Controller
{
    private $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @Route("/group/{group}", defaults={"page": "1"}, name="ideasByGroup")
     * @Route("/group/{group}/{page}", requirements={"page": "[1-9]\d*"}, name="ideasByGroup_paginated"))
     */
    public function __invoke(Group $group, int $page): Response
    {
        $ideas = $this->bus->handle(
            new GetIdeasByGroupQuery
            (
                $page, $group
            )
        );

        return $this->render('frontend/idea/ideasByGroup.html.twig', [
            'ideas' => $ideas,
        ]);

    }


}
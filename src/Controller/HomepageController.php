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

use App\Command\GetIdeasByPageQuery;
use League\Tactician\CommandBus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomepageController extends Controller
{
    /**
     * @var CommandBus
     */
    private $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @Route("/", defaults={"page": "1"}, name="homepage")
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="homepage_paginated")
     */
    public function __invoke(int $page): Response
    {
        $ideas = $this->bus->handle(
            new GetIdeasByPageQuery(
                $page
            )
        );

        return $this->render('frontend/homepage.html.twig', [
            'ideas' => $ideas,
        ]);
    }
}

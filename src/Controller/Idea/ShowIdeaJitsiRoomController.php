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

namespace App\Controller\Idea;

use App\Entity\Idea;
use App\MessageBus\QueryBus;
use App\Messenger\Idea\GetIdeaJitsiRoomUrlQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/idea/{slug}/jitsi", name="idea_jitsi", methods={"GET"})
 */
class ShowIdeaJitsiRoomController extends AbstractController
{
    /**
     * @var QueryBus
     */
    private $queryBus;

    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    public function __invoke(Idea $idea): Response
    {
        if (!$idea->isOnline()) {
            throw $this->createNotFoundException();
        }

        $url = $this->queryBus->query(
            new GetIdeaJitsiRoomUrlQuery($idea)
        );

        if ($idea->isJitsiRoomOpen()) {
            return $this->redirect($url);
        }

        return $this->render('frontend/idea/jitsi.html.twig', [
            'idea' => $idea,
            'url' => $url,
        ]);
    }
}

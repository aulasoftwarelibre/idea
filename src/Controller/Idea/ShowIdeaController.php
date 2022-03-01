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
use App\Services\Seo\ConfigureOpenGraphService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/idea/{slug}", name="idea_show")
 */
class ShowIdeaController extends AbstractController
{
    public function __construct(
        private ConfigureOpenGraphService $openGraphService
    ) {
    }

    public function __invoke(Idea $idea, Request $request): Response
    {
        $item = $idea->getImage()?->getName() ? $idea : $idea->getGroup();

        $this->openGraphService->configure(
            $idea->getTitle(),
            $idea->getDescription(),
            $item
        );

        return $this->render('frontend/idea/show.html.twig', [
            'complete' => true,
            'idea' => $idea,
        ]);
    }
}

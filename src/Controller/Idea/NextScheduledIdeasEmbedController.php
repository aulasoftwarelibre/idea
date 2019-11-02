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

use App\Repository\IdeaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class NextScheduledIdeasEmbedController extends AbstractController
{
    public function __invoke(IdeaRepository $ideaRepository): Response
    {
        $ideas = $ideaRepository->findNextScheduled();

        return $this->render('frontend/idea/_scheduled_ideas_block.html.twig', [
            'title' => 'Próximas actividades',
            'ideas' => $ideas,
        ]);
    }
}

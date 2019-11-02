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
use Leogout\Bundle\SeoBundle\Provider\SeoGeneratorProvider;
use Leogout\Bundle\SeoBundle\Seo\Basic\BasicSeoGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/idea/{slug}", name="idea_show")
 */
class ShowIdeaController extends AbstractController
{
    /**
     * @var SeoGeneratorProvider
     */
    private $seoGeneratorProvider;

    public function __construct(
        SeoGeneratorProvider $seoGeneratorProvider
    ) {
        $this->seoGeneratorProvider = $seoGeneratorProvider;
    }

    public function __invoke(Idea $idea): Response
    {
        $seo = 'basic';

        $this->configureSeoProvider($idea, 'basic');
        $this->configureSeoProvider($idea, 'og');
        $this->configureSeoProvider($idea, 'twitter');

        return $this->render('frontend/idea/show.html.twig', [
            'complete' => true,
            'idea' => $idea,
        ]);
    }

    private function configureSeoProvider(Idea $idea, string $seo): void
    {
        $title = $idea->getTitle();
        $description = mb_substr(strip_tags($idea->getDescription()), 0, 200);

        /** @var BasicSeoGenerator $basicSeoGenerator */
        $basicSeoGenerator = $this->seoGeneratorProvider->get($seo);
        $basicSeoGenerator
            ->setTitle($title)
            ->setDescription($description);
    }
}

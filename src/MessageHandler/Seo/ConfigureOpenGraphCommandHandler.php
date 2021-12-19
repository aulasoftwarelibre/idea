<?php

declare(strict_types=1);

namespace App\MessageHandler\Seo;

use App\Entity\Idea;
use App\Message\Seo\ConfigureOpenGraphCommand;
use App\Repository\IdeaRepository;
use Leogout\Bundle\SeoBundle\Provider\SeoGeneratorProvider;
use Leogout\Bundle\SeoBundle\Seo\AbstractSeoGenerator;
use Leogout\Bundle\SeoBundle\Seo\Basic\BasicSeoGenerator;
use Leogout\Bundle\SeoBundle\Seo\Og\OgSeoGenerator;
use Leogout\Bundle\SeoBundle\Seo\Twitter\TwitterSeoGenerator;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

use function mb_substr;
use function strip_tags;

class ConfigureOpenGraphCommandHandler
{
    public function __construct(
        private CacheManager $cacheManager,
        private IdeaRepository $ideaRepository,
        private RequestStack $requestStack,
        private SeoGeneratorProvider $seoGeneratorProvider,
        private UploaderHelper $uploaderHelper,
    ) {
    }

    public function __invoke(ConfigureOpenGraphCommand $command): void
    {
        $ideaId = $command->getIdeaId();

        $idea = $this->ideaRepository->find($ideaId);
        if (! $idea instanceof Idea) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        $title       = $idea->getTitle();
        $description = mb_substr(strip_tags($idea->getDescription()), 0, 200);
        $asset       = $this->uploaderHelper->asset($idea, 'imageFile') ??
                       $this->uploaderHelper->asset($idea->getGroup(), 'imageFile') ??
                       '/assets/images/twitter.png';
        $path        = $this->cacheManager->getBrowserPath($asset, 'opengraph_thumbnail');

        $this->configureBasicSeo(
            $this->seoGeneratorProvider->get('basic'),
            $title,
            $description,
        );

        $this->configureOpengraphSeo(
            $this->seoGeneratorProvider->get('og'),
            $title,
            $description,
            $path
        );

        $this->configureTwitterSeo(
            $this->seoGeneratorProvider->get('twitter'),
            $title,
            $description,
            $path
        );
    }

    private function configureBasicSeo(
        BasicSeoGenerator|AbstractSeoGenerator $basicSeoGenerator,
        string $title,
        string $description,
    ): void {
        $basicSeoGenerator
            ->setTitle($title)
            ->setDescription($description);
    }

    private function configureOpengraphSeo(
        OgSeoGenerator|AbstractSeoGenerator $ogSeoGenerator,
        string $title,
        string $description,
        string $uri
    ): void {
        $ogSeoGenerator
            ->setTitle($title)
            ->setDescription($description)
            ->setImage($uri)
            ->set('og:image:width', 1200)
            ->set('og:image:height', 600);
    }

    private function configureTwitterSeo(
        TwitterSeoGenerator|AbstractSeoGenerator $twitterSeoGenerator,
        string $title,
        string $description,
        string $uri
    ): void {
        $twitterSeoGenerator
            ->setTitle($title)
            ->setDescription($description)
            ->setImage($uri);
    }
}

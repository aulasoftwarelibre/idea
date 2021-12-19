<?php

declare(strict_types=1);

namespace App\Services\Seo;

use Leogout\Bundle\SeoBundle\Provider\SeoGeneratorProvider;
use Leogout\Bundle\SeoBundle\Seo\AbstractSeoGenerator;
use Leogout\Bundle\SeoBundle\Seo\Basic\BasicSeoGenerator;
use Leogout\Bundle\SeoBundle\Seo\Og\OgSeoGenerator;
use Leogout\Bundle\SeoBundle\Seo\Twitter\TwitterSeoGenerator;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

use function mb_substr;
use function strip_tags;

final class ConfigureOpenGraphService
{
    public function __construct(
        private CacheManager $cacheManager,
        private SeoGeneratorProvider $seoGeneratorProvider,
        private UploaderHelper $uploaderHelper,
    ) {
    }

    public function configure(string $title, string $description, OpenGraphItemInterface $openGraphItem): void
    {
        $description = mb_substr(strip_tags($description), 0, 200);
        $asset       = $this->uploaderHelper->asset($openGraphItem, 'imageFile') ??
                       '/assets/images/seo.png';
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

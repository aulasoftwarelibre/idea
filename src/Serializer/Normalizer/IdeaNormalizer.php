<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\Idea;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class IdeaNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private ObjectNormalizer $normalizer;
    private RouterInterface $router;

    public function __construct(ObjectNormalizer $normalizer, RouterInterface $router)
    {
        $this->normalizer = $normalizer;
        $this->router     = $router;
    }

    /**
     * @{@inheritDoc}
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        if (! $object instanceof Idea) {
            return $this->normalizer->normalize($object, $format, $context);
        }

        return [
            'id' => $object->getId(),
            'title' => $object->getTitle(),
            'description' => $object->getGroup()->getName(),
            'url' => $this->router->generate('idea_show', ['slug' => $object->getSlug()], RouterInterface::ABSOLUTE_URL),
        ];
    }

    /**
     * @{@inheritDoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Idea;
    }

    /**
     * @{@inheritDoc}
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

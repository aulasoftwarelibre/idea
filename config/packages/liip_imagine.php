<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('liip_imagine', [
        'driver' => 'gd',
        'cache' => 'default',
        'data_loader' => 'default',
        'filter_sets' => [
            'squared_thumbnail' => [
                'jpeg_quality' => 85,
                'png_compression_level' => 8,
                'filters' => [
                    'auto_rotate' => true,
                    'strip' => true,
                    'thumbnail' => [
                        'size' => [80, 80],
                        'mode' => 'outbound',
                        'allow_upscale' => true,
                    ],
                ],
            ],
        ],
        'resolvers' => [
            'default' => [
                'web_path' => [
                    'cache_prefix' => 'cache/'
                ],
            ],
        ],
    ]);
};

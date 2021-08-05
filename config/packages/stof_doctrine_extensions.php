<?php

declare(strict_types=1);

use Symfony\Config\StofDoctrineExtensionsConfig;

return static function (StofDoctrineExtensionsConfig $stofDoctrineExtensions): void {
    $stofDoctrineExtensions->defaultLocale('es_ES');
    $stofDoctrineExtensions->orm('default')
        ->sluggable(true)
        ->timestampable(true)
        ->softdeleteable(true)
    ;
};

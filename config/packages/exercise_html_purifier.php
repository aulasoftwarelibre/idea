<?php

declare(strict_types=1);

use Symfony\Config\ExerciseHtmlPurifierConfig;


return static function (ExerciseHtmlPurifierConfig $purifier): void {
    $purifier
        ->defaultCacheSerializerPath('%kernel.cache_dir%/htmlpurifier');

    $purifier->htmlProfiles('default')
        ->config('Core.Encoding', 'UTF-8');

    $purifier->htmlProfiles('summary')
        ->config('HTML.ForbiddenElements', ['a']);
};

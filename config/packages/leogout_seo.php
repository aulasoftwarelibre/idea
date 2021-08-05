<?php

declare(strict_types=1);

use Symfony\Config\LeogoutSeoConfig;

return static function (LeogoutSeoConfig $leogoutSeo): void {
    $leogoutSeo->general()
        ->title('Ideas | Aula de Software Libre')
        ->description('Portal de propuesta de ideas y actividades del Aula de Software Libre de la Universidad de Córdoba');

    $leogoutSeo->og()
        ->type('website')
        ->url('https://ideas.aulasoftwarelibre.uco.es/')
        ->image('https://ideas.aulasoftwarelibre.uco.es/assets/images/facebook.png');

    $leogoutSeo->twitter()
        ->card('summary')
        ->site('@aulasl')
        ->image('https://ideas.aulasoftwarelibre.uco.es/assets/images/twitter.png');
};


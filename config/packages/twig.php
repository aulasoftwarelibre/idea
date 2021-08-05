<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig, ContainerConfigurator $container) {
    $twig
        ->path('%kernel.project_dir%/templates', null)
        ->path('%kernel.project_dir%/assets/css', 'css')
        ->debug('%kernel.debug%')
        ->strictVariables('%kernel.debug%')
        ->formThemes([
            '@FOSCKEditor/Form/ckeditor_widget.html.twig',
            '@SemanticUi/form/semantic_2_layout.html.twig',
            'form/vich_widget.html.twig',
            'form/semantic_ui.html.twig',
        ])
    ;

    if ('test' === $container->env()) {
        $twig->strictVariables(true);
    }
};


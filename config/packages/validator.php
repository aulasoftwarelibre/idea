<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework, ContainerConfigurator $container): void {
    $framework->validation()
        ->emailValidationMode('html5');

    if ('test' === $container->env()) {
        $framework->validation()->notCompromisedPassword()->enabled(false);
    }
};

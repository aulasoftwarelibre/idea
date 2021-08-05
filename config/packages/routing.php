<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework, ContainerConfigurator $container): void {
    $framework->router()
        ->utf8(true)
    ;

    if ('dev' === $container->env()) {
        $framework->router()
            ->strictRequirements(true)
        ;
    }

    if ('prod' === $container->env()) {
        $framework->router()->strictRequirements(null);
    }
};

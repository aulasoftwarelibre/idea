<?php

declare(strict_types=1);


use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DebugConfig;

return
    #[When(env: 'dev')]
    #[When(env: 'test')]
    static function (DebugConfig $debug, ContainerConfigurator $container): void {
        if ('dev' === $container->env()) {
            $debug->dumpDestination('tcp://%env(VAR_DUMPER_SERVER)%');
        }
    };

<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\MonologConfig;

return static function (MonologConfig $monolog, ContainerConfigurator $container): void {
    if ('dev' === $container->env()) {
        $mainHandler = $monolog->handler('main');
        $mainHandler->type('stream');
        $mainHandler->path('%kernel.logs_dir%/%kernel.environment%.log');
        $mainHandler->level('debug');
        $mainHandler->channels()->elements(['!event']);

        $consoleHandler = $monolog->handler('console');
        $consoleHandler->type('console');
        $consoleHandler->processPsr3Messages(false);
        $consoleHandler->channels()->elements(["!event", "!doctrine", "!console"]);
    }

    if ('test' === $container->env()) {
        $mainHandler = $monolog->handler('main');
        $mainHandler->type('fingers_crossed');
        $mainHandler->actionLevel('error');
        $mainHandler->handler('nested');
        $mainHandler->channels()->elements(['!event']);
        $mainHandler->excludedHttpCode()->code(404);
        $mainHandler->excludedHttpCode()->code(405);

        $nestedHandler = $monolog->handler('nested');
        $nestedHandler->type('stream');
        $nestedHandler->path('%kernel.logs_dir%/%kernel.environment%.log');
        $nestedHandler->level('debug');
    }

    if ('prod' === $container->env()) {
        $mainHandler = $monolog->handler('main');
        $mainHandler->type('fingers_crossed');
        $mainHandler->actionLevel('error');
        $mainHandler->handler('nested');
        $mainHandler->excludedHttpCode()->code(404);
        $mainHandler->excludedHttpCode()->code(405);
        $mainHandler->bufferSize(50);

        $nestedHandler = $monolog->handler('nested');
        $nestedHandler->type('stream');
        $nestedHandler->path('%kernel.logs_dir%/%kernel.environment%.log');
        $nestedHandler->level('debug');

        $consoleHandler = $monolog->handler('console');
        $consoleHandler->type('console');
        $consoleHandler->processPsr3Messages(false);
        $consoleHandler->channels()->elements(["!event", "!doctrine"]);
    }
};

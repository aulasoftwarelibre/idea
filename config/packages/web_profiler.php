<?php

declare(strict_types=1);


use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;
use Symfony\Config\WebProfilerConfig;

return
    #[When(env: 'dev')]
    #[When(env: 'test')]
    static function (WebProfilerConfig $profiler, FrameworkConfig $framework, ContainerConfigurator $container): void {
        if ('dev' === $container->env()) {
            $profiler
                ->toolbar(true)
                ->interceptRedirects(false);

            $framework->profiler()
                    ->onlyExceptions(false);
        }

        if ('test' === $container->env()) {
            $profiler
                ->toolbar(false)
                ->interceptRedirects(false);

            $framework->profiler()
                ->collect(false);
        }
    };

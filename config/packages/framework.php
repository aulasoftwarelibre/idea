<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework, ContainerConfigurator $container): void {
    $framework->secret('%env(APP_SECRET)%');

    $framework->csrfProtection()
        ->enabled(true);

    $framework->session()
        ->handlerId(null)
        ->cookieSecure('auto')
        ->cookieSamesite('lax');

    $framework->phpErrors()
        ->log(true);

    if ('test' === $container->env()) {
        $framework->test(true);
        $framework->session()
            ->storageId('session.storage.mock_file');
    }

    if ('prod' === $container->env()) {
        $framework->trustedProxies('172.16.8.2');
        $framework->trustedHeaders(['x-forwarded-proto']);
    }
};

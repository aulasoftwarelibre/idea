<?php

declare(strict_types=1);

use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DoctrineConfig;
use Symfony\Config\FrameworkConfig;

return static function (DoctrineConfig $doctrine, FrameworkConfig $framework, ContainerConfigurator $container): void {
    $doctrine->dbal()
        ->connection('default')
        ->url('%env(resolve:DATABASE_URL)%');

    $doctrine->orm()
        ->autoGenerateProxyClasses(true);

    $defaultManager = $doctrine->orm()->entityManager('default');
    $defaultManager->autoMapping(true);
    $defaultManager->namingStrategy('doctrine.orm.naming_strategy.underscore_number_aware');

    $appMapping = $defaultManager->mapping('App');
    $appMapping
        ->isBundle(false)
        ->type('annotation')
        ->dir('%kernel.project_dir%/src/Entity')
        ->prefix('App\Entity')
        ->alias('App');

    $defaultManager->filter('softdeleteable')->class(SoftDeleteableFilter::class)->enabled(true);

    if ('prod' === $container->env()) {
        $doctrine->orm()->autoGenerateProxyClasses(false);
        $defaultManager->metadataCacheDriver()->type('pool')->pool('doctrine.system_cache_pool');
        $defaultManager->queryCacheDriver()->type('pool')->pool('doctrine.system_cache_pool');
        $defaultManager->resultCacheDriver()->type('pool')->pool('doctrine.system_cache_pool');

        $framework->cache()->pool('doctrine.result_cache_pool')->adapters(['cache.app']);
        $framework->cache()->pool('doctrine.system_cache_pool')->adapters(['cache.system']);
    }
};

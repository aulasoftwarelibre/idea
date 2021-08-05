<?php

declare(strict_types=1);


use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import('@FOSJsRoutingBundle/Resources/config/routing/routing-sf4.xml');
};

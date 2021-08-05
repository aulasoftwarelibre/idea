<?php

declare(strict_types=1);


use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import('@UcoOAuth2ClientBundle/Resources/config/routing/security.xml');
};

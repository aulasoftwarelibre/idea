<?php

declare(strict_types=1);


use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {

    $routes->add('help', '/help')
        ->controller('Symfony\Bundle\FrameworkBundle\Controller\TemplateController::templateAction')
        ->defaults(['template' => 'frontend/static/help.html.twig', 'maxAge' => 86400, 'sharedAge' => 86400]);

    $routes->add('cookies', '/cookies')
        ->controller('Symfony\Bundle\FrameworkBundle\Controller\TemplateController::templateAction')
        ->defaults(['template' => 'frontend/static/cookies.html.twig', 'maxAge' => 86400, 'sharedAge' => 86400]);

    $routes->add('terms', '/terms')
        ->controller('Symfony\Bundle\FrameworkBundle\Controller\TemplateController::templateAction')
        ->defaults(['template' => 'frontend/static/terms.html.twig', 'maxAge' => 86400, 'sharedAge' => 86400]);
};

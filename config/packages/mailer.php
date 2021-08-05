<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework, ContainerConfigurator $container): void {
    $framework->mailer()
        ->dsn('%env(MAILER_DSN)%');

    if ('dev' === $container->env()) {
        $framework->mailer()
            ->envelope()
                ->recipients(['%mail_from%'])
        ;
    }
};

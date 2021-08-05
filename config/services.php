<?php


use App\Menu\MenuBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function(ContainerConfigurator $container) {
    $container->parameters()
        ->set('mail_from', 'aulasoftwalibre@uco.es')
        ->set('policy_version', '20191126161857')
        ->set('jitsi_prefix', 'AulaSoftwareLibre')
    ;

    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
            ->bind('$assetsPath', '%kernel.project_dir%/assets')
            ->bind('$mailFrom', '%mail_from%')
            ->bind('$policyVersion', '%policy_version%')
            ->bind('$jitsiPrefix', '%jitsi_prefix%')
        ;

    $services->load('App\\', '../src/*')
        ->exclude('../src/{DependencyInjection,Entity,Tests,Kernel.php}')
    ;

    $services->load('App\\Controller\\', '../src/Controller/')
        ->tag('controller.service_arguments')
    ;

    $services->load('App\\MessageHandler\\', '../src/MessageHandler/**/*CommandHandler.php')
        ->autoconfigure(false)
        ->tag('messenger.message_handler', ['bus' => 'command.bus']);

    $services->load('App\\MessageHandler\\', '../src/MessageHandler/**/*QueryHandler.php')
        ->autoconfigure(false)
        ->tag('messenger.message_handler', ['bus' => 'query.bus']);

    $services->set('menu_builder', MenuBuilder::class)
        ->args([service('knp_menu.factory')])
        ->tag('knp_menu.menu_builder', ['method' => 'createMainMenu', 'alias' => 'main'])
        ->tag('knp_menu.menu_builder', ['method' => 'createGroupMenu', 'alias' => 'groups'])
        ->tag('knp_menu.menu_builder', ['method' => 'profileMenu', 'alias' => 'profile'])
    ;
};

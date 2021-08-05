<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Uco\OAuth2\Client\Provider\Uco;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('knpu_oauth2_client', [
        'clients' => [
            'uco' => [
                'type' => 'generic',
                'provider_class' => Uco::class,
                'client_id' => '%env(CLIENT_UCO_ID)%',
                'client_secret' => '%env(CLIENT_UCO_SECRET)%',
                'redirect_route' => 'connect_uco_check',
            ],
            'google' => [
                'type' => 'google',
                'client_id' => '%env(CLIENT_GOOGLE_ID)%',
                'client_secret' => '%env(CLIENT_GOOGLE_SECRET)%',
                'redirect_route' => 'connect_google_check',
            ],
            'github' => [
                'type' => 'github',
                'client_id' => '%env(CLIENT_GITHUB_ID)%',
                'client_secret' => '%env(CLIENT_GITHUB_SECRET)%',
                'redirect_route' => 'connect_github_check',
            ],
            'discord' => [
                'type' => 'discord',
                'client_id' => '%env(CLIENT_DISCORD_ID)%',
                'client_secret' => '%env(CLIENT_DISCORD_SECRET)%',
                'redirect_route' => 'connect_discord_check',
            ],
        ],
    ]);
};

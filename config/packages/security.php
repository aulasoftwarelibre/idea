<?php

declare(strict_types=1);

use App\Entity\User;
use App\Security\User\UserChecker;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security) {
    $security->encoder(User::class)->algorithm('auto');

    $security->roleHierarchy('ROLE_SUPER_ADMIN', ['ROLE_ADMIN', 'ROLE_ALLOWED_TO_SWITCH']);

    $security->provider('app_user_provider')
        ->entity()
        ->class(User::class)
        ->property('username');

    $devFw = $security->firewall('dev');
    $devFw->pattern('^/(_(profiler|wdt)|css|images|js)/');
    $devFw->security(false);

    $mainFw = $security->firewall('main');
    $mainFw->provider('app_user_provider');
    $mainFw->pattern('^/');
    $mainFw->userChecker(UserChecker::class);
    $mainFw->anonymous();
    $mainFw->lazy(true);
    $mainFw->switchUser();
    $mainFw->guard()->authenticators([
        'uco_oauth2_client.security_guard.uco_authenticator',
        'App\Security\Guard\GoogleAuthenticator',
        'App\Security\Guard\GithubAuthenticator',
        'App\Security\Guard\DiscordAuthenticator',
    ]);
    $mainFw->guard()->entryPoint('uco_oauth2_client.security_guard.uco_authenticator');

    $security->accessControl()
        ->path('^/connect/(uco|google|discord|github)/check')->roles(['ROLE_USER']);
    $security->accessControl()
        ->path('^/admin')->roles(['ROLE_ADMIN']);
    $security->accessControl()
        ->path('^/')->roles(['IS_AUTHENTICATED_ANONYMOUSLY']);

};

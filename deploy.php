<?php

// See this article : https://foobarflies.io/a-sensible-deployerphp-config-for-sf4

namespace Deployer;

require 'recipe/symfony4.php';

set('ssh_type', 'native');
set('ssh_multiplexing', true);

// Configuration

// For the repository, master is implied
set('repository', 'https://github.com/aulasoftwarelibre/idea.git');

// Set shared dirs and dirs for Symfony 4
// I share sessions so that atomic builds do not "logout" all users 
// if users I have. This may be a problem if your deployment
// modifies your session system somehow, so be careful
// public/uploads is my standard upload directory for files
// and must be shared between deploys obviously.
set('shared_dirs', ['var/log', 'var/sessions', 'public/uploads']);
set('writable_dirs', ['var', 'public/uploads']);

// Paths to clear
// To avoid leaving unwanted access to these files in production,
// I simply clear what I don't need to run the app, and I run the 
// clear:path _after_ everything has been built.
set('clear_paths', [
  './README.md',
  './.gitignore',
  './.git',
  './.php_cs',
  './.env.dist',
  './.env',
  './.eslintrc',
  './.babelrc',
  '/assets',
  '/tests',
  './package.json',
  './package-lock.json',
  './symfony.lock',
  './webpack.config.js',
  './postcss.config.js',
  './phpunit.xml',
  './phpunit.xml.dist',
  './deploy.php',
  './psalm.xml',
  './composer.phar',
  './composer.lock',
  // We keep composer.json as it's needed by 
  // the Kernel now in Symfony 4
]);

// Set env, else composer will fail
// This is new since Sf3.4 I think, where we use a .env
// file instead of the parameters.yml file. Without these
// parameters, deployer will choke on deploy:vendors
set('env', function () {
    return [
        'APP_ENV' => 'prod',
        'MAILER_URL' => 'null://localhost',
        // Add more if you have other parameters in your .env
    ];
});

// Servers
// This is easy, just the server with a stage name so you can call
// `deploy production`
host('production')
    ->hostname('rrycsic15.uco.es')
    ->user('sergio')
    ->forwardAgent()
    ->set('deploy_path', '/home/sergio/my_project');

set('default_stage', 'production');
set('http_user', 'www-data');

// Tasks
// If you can / want to build assets locally and then upload, 
// if for instance you don't have the build tools on your frontend
// server.
desc('Build CSS/JS and deploy local built files');
task('deploy:build_local_assets', function () {
    runLocally('yarn install');
    runLocally('yarn run build');
    upload('./public/build', '{{release_path}}/public/.');
});

// A simple task to restart the PHP FPM service, 
// if you use it of course
desc('Restart PHP-FPM service');
task('php-fpm:restart', function () {
    // The user must have rights for restart service
    // Change with your exact service version
    run('sudo systemctl restart php7.1-fpm.service');
});
after('deploy:symlink', 'php-fpm:restart');

// If deploy fails, automatically unlock
after('deploy:failed', 'deploy:unlock');

/**
 * The main task - it's basically the same as the symfony4
 * one but with rearranged tasks (especialy for clear_paths)
 * and added tasks (assets, cache, etc)
 */
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:build_local_assets',  // Choose which version
    'deploy:cache:clear',
    'deploy:cache:warmup',
    'deploy:writable',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy');

// Display success message on completion
after('deploy', 'success');


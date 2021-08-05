<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->messenger()
        ->defaultBus('command.bus');

    $busConfig = $framework->messenger()->bus('command.bus');
    array_map(
        fn ($id) => $busConfig->middleware()->id($id),
        ['validation', 'doctrine_transaction']
    );

    $busConfig = $framework->messenger()->bus('query.bus');
    array_map(
        fn ($id) => $busConfig->middleware()->id($id),
        ['validation']
    );

    $busConfig = $framework->messenger()->bus('event.bus')->defaultMiddleware('allow_no_handlers');
    array_map(
        fn ($id) => $busConfig->middleware()->id($id),
        ['validation']
    );
};

<?php

declare(strict_types=1);

use Symfony\Config\KnpMenuConfig;

return static function (KnpMenuConfig $knpMenu): void {
    $knpMenu->twig()
        ->template('@SemanticUi/menu/semantic_2_menu.html.twig')
    ;
};

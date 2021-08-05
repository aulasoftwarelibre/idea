<?php

declare(strict_types=1);

use Symfony\Config\SensioFrameworkExtraConfig;

return static function (SensioFrameworkExtraConfig $sensioFramework): void {
    $sensioFramework->router()
        ->annotations(false);
};

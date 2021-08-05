<?php

declare(strict_types=1);

use Symfony\Config\WebpackEncoreConfig;

return static function(WebpackEncoreConfig $webpackEncore) {
    $webpackEncore->outputPath('%kernel.project_dir%/public/build');
    $webpackEncore->scriptAttributes('defer', true);
};

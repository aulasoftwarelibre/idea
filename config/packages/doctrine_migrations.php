<?php

declare(strict_types=1);

use Symfony\Config\DoctrineMigrationsConfig;

return static function(DoctrineMigrationsConfig $migrations):void {
    $migrations
        ->migrationsPath('DoctrineMigrations', '%kernel.project_dir%/migrations')
        ->storage()
            ->tableStorage()
                ->tableName('migration_versions')
        ;
};

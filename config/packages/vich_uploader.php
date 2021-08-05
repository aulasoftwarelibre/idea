<?php

declare(strict_types=1);

use Symfony\Config\VichUploaderConfig;

return static function (VichUploaderConfig $vichUploader) {
    $vichUploader->dbDriver('orm');

    $vichUploader->mappings('avatars')
        ->uriPrefix('/images/avatars')
        ->uploadDestination( '%kernel.project_dir%/public/images/avatars')
        ->injectOnLoad(false)
        ->deleteOnRemove(true)
        ->deleteOnUpdate(true)
        ->namer()->service('vich_uploader.namer_uniqid');

    $vichUploader->mappings('ideas')
        ->uriPrefix('/images/ideas')
        ->uploadDestination( '%kernel.project_dir%/public/images/ideas')
        ->injectOnLoad(false)
        ->deleteOnRemove(true)
        ->deleteOnUpdate(true)
        ->namer()->service('vich_uploader.namer_uniqid');
};

<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'fos_ck_editor',
        [
            'base_path' => 'build/ckeditor4',
            'js_path' => 'build/ckeditor4/ckeditor.js',
            'default_config' => 'simple_toolbar',
            'configs' => [
                'simple_toolbar' => [
                    'basicEntities' => false,
                    'entities_greek' => false,
                    'entities_latin' => false,
                    'entities_additional' => '',
                    'fillEmptyBlocks' => false,
                    'tabSpaces' => 0,
                    'toolbar' => [
                        [
                            'Bold',
                            'Italic',
                            'Strike',
                            'Link',
                        ],
                        [
                            'BulletedList',
                            'NumberedList',
                            '-',
                            'Outdent',
                            'Indent',
                        ],
                        [
                            'Copy',
                            'Paste',
                            'PasteFromWord',
                            '-',
                            'Undo',
                            'Redo',
                        ],
                        ['Source'],
                    ],
                ],
            ],
        ]
    );
};

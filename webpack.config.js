var Encore = require('@symfony/webpack-encore');

Encore
    .addEntry('app', './assets/js/app.js')
    .addEntry('login', './assets/js/login.js')
    .addEntry('admin', './assets/js/admin.js')
    .cleanupOutputBeforeBuild()
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .copyFiles([
        {from: './node_modules/ckeditor4/', to: 'ckeditor4/[path][name].[ext]', pattern: /\.(js|css)$/, includeSubdirectories: false},
        {from: './node_modules/ckeditor4/adapters', to: 'ckeditor4/adapters/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/lang', to: 'ckeditor4/lang/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/plugins', to: 'ckeditor4/plugins/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/skins', to: 'ckeditor4/skins/[path][name].[ext]'}
    ])
    .enableBuildNotifications()
    .enableSingleRuntimeChunk()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .splitEntryChunks()
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();

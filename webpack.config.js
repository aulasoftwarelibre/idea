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

var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .autoProvidejQuery()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .addEntry('js/app', [
        './assets/js/app.js'
    ])
    .addStyleEntry('css/app', [
        './assets/css/app.css',
        './vendor/friendsofsymfony/comment-bundle/Resources/public/css/comments.css'
    ])
;

module.exports = Encore.getWebpackConfig();

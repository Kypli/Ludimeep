const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')

    // load picture to public from assets
    .copyFiles({
        from: './assets/image',

        // optional target path, relative to the output dir
        to: 'image/[path][name].[ext]',

        // if versioning is enabled, add the file hash too
        //to: 'images/[path][name].[hash:8].[ext]',

        // only copy files matching this pattern
        //pattern: /\.(png|jpg|jpeg)$/
    })

    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')

    // Home
    .addStyleEntry('home', './assets/styles/home/style.css')

    // Operation
    .addEntry('operation', './assets/js/operation/operation.js')

    // Sondage
    .addEntry('sondage', './assets/js/sondage/sondage.js')
    .addStyleEntry('sondage_show', './assets/styles/sondage/sondage.css')

    // Table
    .addEntry('table', './assets/js/table/table.js')

    // Tchat
    .addEntry('tchat', './assets/js/tchat/tchat.js')

    // User
    .addStyleEntry('user', './assets/styles/user/index.css')
    .addStyleEntry('user_fiche', './assets/styles/user/style.css')

    // Actu
    .addEntry('actu', './assets/js/actu/actu.js')
    .addStyleEntry('actu_show', './assets/styles/actu/actu.css')

    // Photo
    .addEntry('photo', './assets/js/photo/photo.js')

    // Jeu
    .addEntry('game', './assets/js/game/game.js')

    // Dicussion
    .addStyleEntry('discussion', './assets/styles/discussion/style.css')

    // Séance
    .addStyleEntry('seance', './assets/styles/seance/index.css')
    .addStyleEntry('seance_show', './assets/styles/seance/show.css')

    // Service
    .addEntry('timer', './assets/js/service/timer.js')
    .addEntry('shieldui', './assets/js/service/shieldui.js')
    .addEntry('modal_photo', './assets/js/service/modal_photo.js')

    // Interact
    .addEntry('interact_actu', './assets/js/interact/actu.js')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    // .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();

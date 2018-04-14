var Encore = require('@symfony/webpack-encore');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const sassVars = require('./assets/js/Components/theme');
const sass = require('node-sass');
const sassUtils = require('node-sass-utils')(sass);

Encore
    // the project directory where all compiled assets will be stored
    .setOutputPath('public/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // allow to load jQuery once in order to don't load for each files that require this module
    .createSharedEntry('layout','./assets/js/layout.js')

    // will create public/build/app.js and public/build/thesaurus.css
    .addEntry('thesaurus', './assets/js/thesaurus.js')

    // .addPlugin(new sassUtils())
    // allow sass/scss files to be processed
    //Code of get function from https://itnext.io/sharing-variables-between-js-and-sass-using-webpack-sass-loader-713f51fa7fa0
    .enableSassLoader(function(loaderOptions){
            loaderOptions.functions = {
                "get($keys)": function(keys) {
                    keys = keys.getValue().split(".");
                    let result = sassVars;
                    let i;
                    for (i = 0; i < keys.length; i++) {
                        result = result[keys[i]];
                    }
                    result = sassUtils.castToSass(result);
                    return result;
                }
            }
        }
    )

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

    // create hashed filenames (e.g. app.abc123.css)
    .enableVersioning()

    .addPlugin(new CopyWebpackPlugin([
        {from: './assets/static', to: 'static'}
    ]))
;

// export the final configuration
module.exports = Encore.getWebpackConfig();
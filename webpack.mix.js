const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.webpackConfig({ node: { fs: 'empty' }});

mix.setPublicPath('public');

//mix.copy('node_modules/foo/bar.css', 'public/css/bar.css');

mix
    .sass('resources/sass/frontend/app.scss', 'css/frontend.css')
    .sass('resources/sass/backend/app.scss', 'css/backend.css')
    .js('resources/js/frontend/app.js', 'js/frontend.js')
    .js('resources/js/frontend/payment.js', 'js/payment.js')
    .js([
        'resources/js/backend/before.js',
        'resources/js/backend/app.js',
        'resources/js/backend/after.js',
        'resources/js/backend/pdfmake.min.js',
        'resources/js/backend/vfs_fonts.js',
        'resources/js/backend/datatables.min.js',
        'resources/js/backend/buttons.server-side.js',
        'resources/js/backend/intlTelInput.js',
        'resources/js/backend/bootstrap-native-v4.min.js'
    ], 'js/backend.js')
    .extract([
        //'jquery',
        //'bootstrap',
        //'popper.js/dist/umd/popper',
    ]);

if (mix.inProduction() || process.env.npm_lifecycle_event !== 'hot') {
    mix.options({
        terser: {
            terserOptions: {
                compress: {
                    drop_console: true
                }
            }
        }
    });
    mix.version();
}

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
let mix = require('laravel-mix');
mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');

mix.scripts(
    [
        'resources/js/tools.js',
        'resources/js/text_counter.js',
    ],
    'public/js/lms-tools.js'
);

mix.copyDirectory('vendor/tinymce/tinymce', 'public/js/tinymce');

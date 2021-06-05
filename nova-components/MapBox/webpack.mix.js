let mix = require('laravel-mix')

mix.setPublicPath('dist')
   .js('resources/js/card.js', 'js')
    .js('resources/js/bsumm.js', 'js')
   .sass('resources/sass/card.scss', 'css')

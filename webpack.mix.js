let mix = require('laravel-mix')

mix.setPublicPath('resources/dist')

mix.js('resources/js/aerial.js', 'aerial.js')
    .sourceMaps()
    .version()

mix.disableSuccessNotifications()
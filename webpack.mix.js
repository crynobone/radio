let mix = require('laravel-mix')

mix.setPublicPath('resources/dist')

mix.js('resources/js/radio.js', 'radio.js')
    .sourceMaps()
    .version()

mix.disableSuccessNotifications()
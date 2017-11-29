const elixir = require('laravel-elixir');

require('laravel-elixir-vue');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

// Include JS from bower
jsFromBower = (scripts) => {
    bower_scripts = []
    scripts.forEach((script) => {
        bower_scripts.push(`../bower/${script}.js`)
    })
    return bower_scripts
}

fileFromBower = (file) => {
    return `resources/assets/bower/${file}`
}

filesFromBowerFolder = (folder, files) => {
    bower_files = []
    files.forEach((file) => {
        bower_files.push(fileFromBower(folder + '/' + file))
    })
    return bower_files
}

elixir(mix => {
    mix
        .browserSync({
            port: 8095,
            open: 'external',
            host: 'yweb.app',
            proxy: 'https://yweb.app:8094',
            https: true,
            ghostMode: false
        })
        .sass('desktop/desktop.scss')
        .sass('mobile/mobile.scss')
        .coffee(['resources/assets/coffee/*.coffee', 'resources/assets/coffee/*/*.coffee'], 'resources/assets/js')
        .copy(fileFromBower('ng-image-gallery/res/icons'), 'public/img/icons')
        .copy(fileFromBower('font-awesome/fonts'), 'public/fonts')
        .scripts(jsFromBower([
            'jquery/dist/jquery',
            'angular/angular.min',
            'angular-resource/angular-resource.min',
            'angular-i18n/angular-locale_ru-ru',
            'underscore/underscore-min',
            'ladda/dist/spin.min',
            'ladda/dist/ladda.min',
            'angular-ladda/dist/angular-ladda.min',
            'moment/min/moment.min',
            'moment/locale/ru',
            'jquery.maskedinput/dist/jquery.maskedinput.min',
            'notifyjs/dist/notify',
            'jquery-ui/jquery-ui.min',
            'jquery.customSelect/jquery.customSelect.min',
            'iCheck/icheck.min',
            'angular-file-upload/dist/angular-file-upload.min',
            'bootstrap/js/bootstrap.min',
            'bootstrap-select/dist/js/bootstrap-select.min',
            'jquery.inputmask/dist/jquery.inputmask.bundle',
            'mark.js/dist/jquery.mark.min',
            'angular-toArrayFilter/toArrayFilter',
            'jquery.cookie/jquery.cookie',
            'jquery.panzoom/dist/jquery.panzoom',
            'angular-animate/angular-animate.min',
            'hammerjs/hammer.min',
            'angular-sanitize/angular-sanitize.min',
            'jquery.actual/jquery.actual.min',
            'mark.js/dist/jquery.mark.min',
        ]).concat(['resources/assets/js/*.js']), 'public/js/scripts.js')
        .scripts('resources/assets/js/mobile/*.js', 'public/js/mobile/scripts.js')
});

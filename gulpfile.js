var elixir = require("laravel-elixir");

elixir(function(mix) {
    mix
        .styles([
            'default/system.css',
            'default/icons.css',
            'default/theme_light.css',
            'default/animate.css',
            'default/style.css',
        ], 'public/default/css/core.css')
        .scripts([
            'default/jquery-2.1.1.min.js',
            'default/angular.min.js',
            'default/angular-animate.min.js',
            'default/dcmsApi.js',
            'default/elastic.js',
            'default/js.js',
        ], 'public/default/js/core.js')
        .scripts([
            'default/highcharts/vue.min.js',
            'default/highcharts/highcharts.js',
            'default/highcharts/vue-highcharts.min.js',
        ], 'public/default/js/highcharts.js')
        .version([
            'default/css/*',
            'default/js/*'
        ]);
});

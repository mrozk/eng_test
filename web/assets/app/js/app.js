'use strict';

angular.module('app', [
    'ngRoute',
    'app.main',
    'app.test'
]);

angular.module('app').config(['$locationProvider', '$routeProvider',
    function config($locationProvider, $routeProvider) {
        $locationProvider.hashPrefix('!');
        $routeProvider.when('/main', {
            template: '<start></start>'
        });

        $routeProvider.when('/test', {
            template: '<test></test>'
        });

        $routeProvider.when('/result', {
            template: '<result></result>'
        });



        $routeProvider.otherwise({redirectTo: '/main'});
    }
]);

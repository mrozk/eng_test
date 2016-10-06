'use strict';

angular.module('app.main', ['ngRoute'])
    .component('start', {
        templateUrl: 'assets/app/js/main/main.html',
        controller: ['$http', '$location',
            function ($http, $location) {
                var self = this;
                self.name = "Тестовое имя";

                $http({
                    method: 'GET',
                    url: '/site/check-start',
                }).then(function successCallback(response) {

                    if(typeof response.data.error != 'undefined'){
                        $location.path("/test");
                    }

                }, function errorCallback(response) {
                });

                this.begin = function (){
                    $http({
                        method: 'GET',
                        url: '/site/start',
                        params: {
                            name: self.name
                        }
                    }).then(function successCallback(response) {
                        console.log(response.data);
                        $location.path("/test");
                    }, function errorCallback(response) {
                    });
                }
            }
        ]
    }).component('result', {
        templateUrl: 'assets/app/js/main/result.html',
        controller: ['$rootScope', function ($rootScope){
            var self = this;
            if(typeof $rootScope['testResult'] != 'undefined'){
                self['testResult'] = true;
            }
            self['msg'] = (typeof $rootScope['msg'] != 'undefined') ? $rootScope['msg'] : 'Неизведанная ошибка';
            $rootScope['testResult'] = null;
            $rootScope['msg'] = null;
        }]
});

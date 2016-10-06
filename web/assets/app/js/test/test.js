'use strict';

angular.module('app.test', ['ngRoute'])
    .component('test', {
        templateUrl: 'assets/app/js/test/test.html',
        controller: ['$http', '$location', '$rootScope', function($http, $location, $rootScope){
            var self = this;
            self.score = 0;
            self.errors = 0;


            self.next = function(){
                self.moreTry = false;
                $http({
                    method: 'GET',
                    url: '/site/next'
                }).then(function successCallback(response) {
                    if(typeof response.data['error'] != 'undefined'){
                        $rootScope['msg'] = response.data['message'];
                        $location.path("/result");
                    }else if(typeof response.data['end'] != 'undefined'){
                        $rootScope['msg'] = 'Тест закончен, набрано балов ' +
                            response.data['score'] +
                            ', ошибок ' +
                            response.data['errors'];
                        $rootScope['testResult'] = true;
                        $location.path("/result");
                    }else {
                        self.currentValue = response.data['currentValue'];
                        self.variants = response.data['variants'];
                        self.score = response.data['score'];
                        self.errors = response.data['errors'];
                        self['answer'] = 0;
                    }

                }, function errorCallback(response) {
                    // called asynchronously if an error occurs
                    // or server returns response with an error status.
                });
            };

            self.next();

            self.check = function(){

                $http({
                    method: 'GET',
                    url: '/site/check',
                    params: {
                        variant: self['answer']
                    }
                }).then(function successCallback(response) {
                    if(typeof response.data['error'] != 'undefined'){
                        $rootScope['msg'] = response.data['message'];
                        $location.path("/result");
                    }else {
                        self.score = response.data['score'];
                        self.errors = response.data['errors'];
                        if(response.data['result'] == 1){
                            self.next();
                        }else if((response.data['result'] == 0) && (response.data['try'] > 1)){
                            self.next();
                        }else {
                            self.moreTry = true;
                        }
                    }


                }, function errorCallback(response) {
                    // called asynchronously if an error occurs
                    // or server returns response with an error status.
                });

            };
            //alert('xxx');
        }]
    });
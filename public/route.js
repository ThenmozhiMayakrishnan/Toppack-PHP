angular.module("toppack").config(function ($routeProvider) {
    'use strict';

    var toppackRouteConfig = {
      controller: 'ToppackController',
      templateUrl: '../views/main.html'
    };

    $routeProvider
      .when('/', toppackRouteConfig)
      .otherwise({
        redirectTo: '/'
      });
});
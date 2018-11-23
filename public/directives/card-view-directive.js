/*
 View Directive
*/
var cardViewDirective = function () {
  'use strict';
  return {
    restrict: "EA",
    templateUrl: "../views/card-view.html",
    scope: {
      repository: "=",
      importRepository: "&"
    },
    link: function(scope, elm, attrs) {
      scope.importRepo = function() {
        scope.importRepository(scope.repository);
      }
    },
  }
}

angular.module("toppack").directive("cardView", cardViewDirective);
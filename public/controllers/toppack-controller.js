function toppackController(ToppackFactory, repositories) {
  'use strict';

  var topPack = this;

  topPack.repositories = repositories;

  topPack.searchTerm = "";
  topPack.searchError = "";

  topPack.searchRepo = function() {
    ToppackFactory.getRepositories(this.searchTerm, getReposSuccessCallback, getReposErrorCallback);
  };

  topPack.importRepository = function(repository, index) {
    ToppackFactory.importRepository(repository, importReposuccessCallback, importRepoErrorCallback);
  }

  var getReposSuccessCallback = function(data) {
    topPack.repositories = data;
  }
  var getReposErrorCallback = function(error) {
    topPack.searchError = "Couldnt find any repositories";
  }

  var importReposuccessCallback = function(response) {
    debugger;
    topPack.repositories[index].imported = true;
    topPack.repositories[index].importError = false;
  }

  var importRepoErrorCallback = function(error) {
    topPack.repositories[index].importError = true;
  }
}

angular.module("toppack").controller("ToppackController",['ToppackFactory', toppackController]);
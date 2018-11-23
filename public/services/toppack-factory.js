function toppackFactory($http) {
  return {
    getRepositories: function(searchTerm, successCallback, errorCallback) {
      var url = "/repositories/" + searchTerm;
      var httpParams = {
        method: 'GET',
        url: url
      }
      $http(httpParams).then(function(response) {
        successCallback(response.data);
      }, function(error) {
        errorCallback(error);
      });
    },

    importRepository: function(repoData, successCallback, errorCallback) {
      var url = "/repository/import"
      var data = repoData;
      var httpParams = {
        method: "POST",
        url: url,
        data: data
      }

      $http(httpParams).then(function(response) {
        successCallback(response);
      }, function(error) {
        errorCallback(error)
      }); 
    }
  }
};

angular.module("toppack").factory("ToppackFactory", toppackFactory);
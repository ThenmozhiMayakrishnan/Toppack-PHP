function toppackFactory($http) {
  return {
    getRepositories: function(searchTerm, successCallback, errorCallback) {
      var url = searchTerm ? "/repositories?searchTerm=" + searchTerm : "/repositories"
      var httpParams = {
        method: 'GET',
        url: url
      }
      $http(httpParams).then(function(response) {
        if(!successCallback) {
          return response.data;
        }
        successCallback(response.data);
      }, function(error) {
        if(!errorCallback) {
          return "Couldn't find repositories";
        }
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
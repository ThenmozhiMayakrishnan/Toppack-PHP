<?php
namespace Handlers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class APIHandler
{
    public static $gitEndpoint = 'https://api.github.com/search/repositories';

    /*
      Fetches the repos based on the keyword passed
    */
    public function getReposByKeyword($keyword) {
        $client = new Client();
        $params = array('q' => $keyword.' in:name',
                        'sort' => 'stars',
                        'order' => 'desc');
        $res = $client->get(self::$gitEndpoint, ['query' => $params]);
        if ($res->getStatusCode() == 200){
          $responseJson = json_decode($res->getBody());
          return $this->whitelistRepo($responseJson);
        }
    }

    public function isPackageJsonExists(string $url) {
        $contentUrl = 'https://api.github.com/repos/'.str_replace('https://github.com/', '', $url).'/contents/package.json';

        $client = new Client();

        // check for package.json
        try{
          $res = $client->get($contentUrl);
          $jsonResponse = json_decode($res->getBody());
          return $jsonResponse->download_url;
        }catch (ClientException $e){
          return array('errorMessage' => 'There is no valid package.json');
        }
    }

    public function parsePackageJson(string $packageJsonUrl, $packageData) {
        // get package.json
        $client = new Client();
        try{
          $res = $client->get($packageJsonUrl);
        }catch (ClientException $e){
          return array('errorMessage' => 'There is no valid package.json');
        }

        return $this->getPackageJsonData($res);
    }

    // whitelist only the attributes we need
    private function whitelistRepo($responseJson){
      $repos = [];
      foreach($responseJson->items as $repo){
        $repository = [
          'name' => $repo->name,
          'description' => $repo->description,
          'url' => $repo->html_url,
          'forks_count' => $repo->forks_count,
          'stars_count' => $repo->stargazers_count,
          'watchers_count' => $repo->watchers_count,
          'author' => $repo->owner->login,
          'avatar_url' => $repo->owner->avatar_url,
          'updated_at' => $repo->updated_at
        ];
        array_push($repos, $repository);
      }
      return $repos;
    }

    private function getPackageJsonData($res) {
      // parse package.json
      if ($res->getStatusCode() == 200){
        $responseJson = json_decode($res->getBody());
        $repository = $this->getDependencies($responseJson);
        $mergedData = array_merge($repository, $packageData);
        return $mergedData;
      } else{
        return array('errorMessage' => 'There is an error connecting to Github.');
      }
    }

    //Parse the JSON response to get dependencies and devDependencies
    private function getDependencies($response_json){
      if(!isset($response_json->devDependencies) && !isset($response_json->dependencies)){
        return array('error' => true, 'message' => 'The package.json has no dependencies.');
      }
      $devDependencies = [];
      $dependencies = [];
      if(isset($response_json->devDependencies)){
        foreach($response_json->devDependencies as $devDep => $versions){
          array_push($devDependencies, $devDep);
        }
      }
      if(isset($response_json->dependencies)) {
        foreach($response_json->dependencies as $dep => $versions){
          array_push($dependencies, $dep);
        }
      }
      $repository['dependencies'] = $dependencies;
      $repository['devDependencies'] = $devDependencies;
      return $repository;
    }
}
 ?>

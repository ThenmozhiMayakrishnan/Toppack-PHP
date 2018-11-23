<?php
namespace Handlers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class APIHandler
{
    public static $git_search_endpoint = 'https://api.github.com/search/repositories';

    //fetches repos based on language and keyword
    public function getGITRepos(string $keyword, $language) {
        $client = new Client();
        $params = array('q' => $keyword.' in:name',
                        'sort' => 'stars',
                        'order' => 'desc');
        $res = $client->get(self::$git_search_endpoint, ['query' => $params]);
        if ($res->getStatusCode() == 200){
          $response_json = json_decode($res->getBody());
          return $this->filterJSON($response_json);
        }
    }

    // sanotizes to only the attributes that we need
    private function filterJSON($response_json){
      $sanitized_results = [];
      foreach($response_json->items as $item){
        $sanitized_item = [
          'updated_at' => $item->updated_at,
          'description' => $item->description,
          'forks_count' => $item->forks_count,
          'html_url' => $item->html_url,
          'name' => $item->name,
          'stargazers_count' => $item->stargazers_count,
          'watchers_count' => $item->watchers_count,
          'owner' => $item->owner->login
        ];
        array_push($sanitized_results, $sanitized_item);
      }
      return $sanitized_results;
    }

    // check if there is a package.json and fetch it
    public function getPackageDotJSON(string $url) {
        $content_url = 'https://api.github.com/repos/'.str_replace('https://github.com/', '', $url).'/contents/package.json';

        $client = new Client();

        // check for package.json
        try{
          $res = $client->get($content_url);
          $json_response = json_decode($res->getBody());
          if (isset($json_response->message)){
            return ['error' => 'There is no package.json file in the project.'];
          }else{
            $package_json_url = $json_response->download_url;
          }
        }catch (ClientException $e){
          return ['error' => 'There is an error connecting to Github.'];
        }

        // get package.json
        try{
          $res = $client->get($package_json_url);
        }catch (ClientException $e){
          return ['error' => 'There is an error connecting to Github.'];
        }

        // parse package.json
        if ($res->getStatusCode() == 200){
          $response_json = json_decode($res->getBody());
          $repository = $this->parseJSON($response_json);
          $repository['url'] = $url;
          return $repository;
        }else{
          return ['error' => 'There is an issue accessing Github.'];
        }
    }

    //Parse the JSON response to get dependencies and devDependencies
    private function parseJSON($response_json){
      if(!isset($response_json->devDependencies) && !isset($response_json->dependencies)){
        return array('error' => 'The package.json has no dependencies.');
      }
      $repository = ['name' => $response_json->name];
      $dependencies = [];
      if(isset($response_json->devDependencies)){
        foreach($response_json->devDependencies as $dep => $versions){
          array_push($dependencies, $dep);
        }
      }
      if(isset($response_json->dependencies)) {
        foreach($response_json->dependencies as $dep => $versions){
          array_push($dependencies, $dep);
        }
      }
      $repository['dependencies'] = $dependencies;
      return $repository;
    }
}
 ?>

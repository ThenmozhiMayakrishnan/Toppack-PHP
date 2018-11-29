<?php
namespace Controllers;

use Psr\Container\ContainerInterface;

class RepositoryController{
  protected $containerInterface;

  public function __construct(ContainerInterface $containerInterface){
      $this->containerInterface = $containerInterface;
  }

  public function getReposBySearchTerm($request, $response, $args) {
      $this->containerInterface->logger->info("getReposBySearchTerm");
      $searchKeyword = $request->getQueryParam('searchTerm', $default = null);
      $this->containerInterface->logger->info($searchKeyword);
      if ($searchKeyword == null){
        $searchResponse = $this->containerInterface->DataHandler->getImportedRepositories($this->containerInterface->pdo);
      } else {
        $data = $this->containerInterface->APIHandler->getReposByKeyword($searchKeyword, $this->containerInterface->logger);
        $searchResponse = $response->withJson($data);
      }
      #$counter = 0;
      #foreach($data as $repo){
        #if ($this->ci->DataHandler->checkIfRepoImported($repo["html_url"], $this->ci->pdo)){
          #$data[$counter]["exists"] = true;
        #}
        #$counter++;
      #}
      $this->containerInterface->logger->info($searchResponse);
      return $searchResponse;
  }

  public function importRepository($request, $response, $args) {
      $postData = $request->getParsedBody();
      $packageJsonUrl = $this->containerInterface->APIHandler->isPackageJsonExists($postData['url']);
      if (array_key_exists("errorMessage", $packageJsonUrl)){
        $importResponse = $response->withJson($packageJsonUrl);
      }else{
        $packageJsonData = $this->containerInterface->APIHandler->parsePackageJson($packageJsonUrl, $postData);
        if (array_key_exists("errorMessage", $packageJsonData)) {
          return $response->withJson($packageJsonData);
        }
        $importResponse = $this->containerInterface->DataHandler->importRepository($packageJsonData, $this->containerInterface->pdo);
      }
      return $response->withJson($importResponse);
  }
}

<?php
namespace Controllers;

use Psr\Container\ContainerInterface;

class SearchController{
  protected $ci;

  public function __construct(ContainerInterface $ci){
      $this->ci = $ci;
  }

  public function __invoke($request, $response, $args) {
      $this->ci->logger->info("Slim-Skeleton '/' route");
      $repository_name = $args['repository-name'];
      $data = $this->ci->APIHandler->getGITRepos($repository_name);

      $newResponse = $response->withJson($data);
      return $newResponse;
  }
}
 ?>

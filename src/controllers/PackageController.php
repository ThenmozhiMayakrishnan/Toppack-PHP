<?php
namespace Controllers;

use Psr\Container\ContainerInterface;

class PackageController{
  protected $containerInterface;

  public function __construct(ContainerInterface $containerInterface){
      $this->ci = $containerInterface;
  }

  public function __invoke($request, $response, $args) {
      $this->ci->logger->info("Slim-Skeleton '/packages/top' route");

      $data = $this->ci->DataHandler->getTopPackages($this->ci->pdo);

      $newResponse = $response->withJson($data);
      return $newResponse;
  }
}

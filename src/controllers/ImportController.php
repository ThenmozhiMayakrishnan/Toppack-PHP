<?php
namespace Controllers;

use Psr\Container\ContainerInterface;

class ImportController{
  protected $ci;

  public function __construct(ContainerInterface $ci){
      $this->ci = $ci;
  }

  public function __invoke($request, $response, $args) {
      $post_data = $request->getParsedBody();
      var_dump($post_data);
      $data = $this->ci->APIHandler->getPackageDotJSON($post_data['html_url']);

      $newResponse = $response->withJson($data);
      return $newResponse;
  }
}
 ?>

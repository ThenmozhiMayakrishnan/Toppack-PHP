<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/', function (Request $request, Response $response, array $args) {
    return $this->renderer->render($response, "/index.html");
});

$app->get('/{name}', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("TopPack API");
    return $response;

});

$app->get('/repositories/{repository-name}', SearchController::class);

$app->post('/repository/import/', ImportController::class);

<?php

use Slim\Http\Request;
use Slim\Http\Response;


$app->get('/', function (Request $request, Response $response, array $args) {

    $this->logger->info("Top Pack index page");
    return $this->renderer->render($response, 'index.html', $args);
});

$app->get('/repositories/top', PackageController::class);

$app->get('/repositories', 'RepositoryController:getReposBySearchTerm');

$app->post('/repository/import', 'RepositoryController:importRepository');

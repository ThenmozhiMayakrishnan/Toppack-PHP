<?php
// DIC configuration
require '../vendor/autoload.php';

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

//PDO
$container['pdo'] = function($c){
    $settings = $c->get('settings')['db'];
    $pdo = new PDO('mysql:host='.$settings['host'].';dbname='.$settings['dbname'],
                    $settings['username'],
                    $settings['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

// controllers
$container['SearchController'] = function($c){
  return new Controllers\SearchController($c);
};

$container['ImportController'] = function($c){
  return new Controllers\ImportController($c);
};

// handlers
$container["APIHandler"] = function($c){
  return new Handlers\APIHandler();
};
$container["StorageHandler"] = function($c){
  return new Handlers\StorageHandler();
};

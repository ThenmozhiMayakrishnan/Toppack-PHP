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
    $config = $c->get('settings')['pdo'];
    try {
      $dsn = "{$config['engine']}:host={$config['host']}";
      $username = $config['username'];
      $password = $config['password'];
      $pdo = new PDO($dsn, $username, $password, $config['options']);
      $pdo->exec("CREATE DATABASE IF NOT EXISTS {$config['database']};") 
      or die(print_r($pdo->errorInfo(), true));
      $dsn = "{$config['engine']}:host={$config['host']};dbname={$config['database']}";
      $pdo = new PDO($dsn, $username, $password, $config['options']);
      return $pdo;
    } catch (PDOException $e) {
      die("DB ERROR: ". $e->getMessage());
    }
};

// controllers
$container['RepositoryController'] = function($c){
  return new Controllers\RepositoryController($c);
};

$container['PackageController'] = function($c){
  return new Controllers\PackageController($c);
};

// handlers
$container["APIHandler"] = function($c){
  return new Handlers\APIHandler();
};
$container["DataHandler"] = function($c){
  return new Handlers\DataHandler();
};

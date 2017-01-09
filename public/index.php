<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//Adding composer classes:
require '../vendor/autoload.php';
//Our own classes under classes directory:
spl_autoload_register(function ($classname) {
    require ("../classes/" . $classname . ".php");
});
//Config file for db connections: Instructions here: https://www.slimframework.com/docs/tutorial/first-app.html#add-config-settings-to-your-application
require '../config/config.php';

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();
//Setting up logging:
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};
//Setting up DB container:
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};
//Setting up views:
$container['view'] = new \Slim\Views\PhpRenderer("../templates/");

//***************** API ***************** 
//Create a route for the API Events 
$app->get('/api/events',function (Request $request, Response $response){
	$this->logger->addInfo("Events");
	$mapper =  new EventMapper($this->db);
	$response = $mapper->getEvents();
	//$response = $this->view->render($response, "events.php", ["events" => $events, "router" => $this->router]);
    return $response;
});

$app->run();



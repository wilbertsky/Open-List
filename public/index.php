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

//Create a route for the home page
$app->get('/yomama',function (Request $request, Response $response){
	$this->logger->addInfo("Home");
	$mapper =  new HomeMapper($this->db);
	$home = $mapper->getHome();
	//$response = $this->view->render($response, "home.php");
	$response = $this->view->render($response, "home.php", ["home" => $home, "router" => $this->router]);
    return $response;
});

//***************** API ***************** 
//Create a route for the API Events 
$app->get('/api/events',function (Request $request, Response $response){
	$this->logger->addInfo("Events");
	$mapper =  new EventMapper($this->db);
	$response = $mapper->getEvents();
	//$response = $this->view->render($response, "events.php", ["events" => $events, "router" => $this->router]);
    return $response;
});


$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});

$app->run();

//Working on events API and response:
/*
$app->post('/events', function (Request $request, Response $response) {
    $this->logger->addInfo("Event list");
    $mapper = new events(this->db);
    $events = $mapper->getEvents();

    $response = $this->view->render($response, "events.php", ["events" => $events]);
    return $response;
});
*/


//Routes examples:
/*
$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});

$app->get('/tickets', function (Request $request, Response $response) {
    $this->logger->addInfo("Ticket list");
    $mapper = new TicketMapper($this->db);
    $tickets = $mapper->getTickets();

    $response->getBody()->write(var_export($tickets, true));
    return $response;
});

$app->post('/ticket/new', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $ticket_data = [];
    $ticket_data['title'] = filter_var($data['title'], FILTER_SANITIZE_STRING);
    $ticket_data['description'] = filter_var($data['description'], FILTER_SANITIZE_STRING);

$app->get('/ticket/{id}', function (Request $request, Response $response, $args) {
    $ticket_id = (int)$args['id'];
    $mapper = new TicketMapper($this->db);
    $ticket = $mapper->getTicketById($ticket_id);

    $response->getBody()->write(var_export($ticket, true));
    return $response;
});
//Example with view:
$app->get('/tickets', function (Request $request, Response $response) {
    $this->logger->addInfo("Ticket list");
    $mapper = new TicketMapper($this->db);
    $tickets = $mapper->getTickets();

    $response = $this->view->render($response, "tickets.phtml", ["tickets" => $tickets]);
    return $response;
});
*/



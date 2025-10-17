<?php

use App\Controllers\HomeController;
use App\Controllers\ContactController; // new line
use App\Router;

$router = new Router();

$router->get('/', HomeController::class, 'index');
$router->get('/contact', ContactController::class, 'show'); // new line
$router->post('/contact', ContactController::class, 'submit'); // new line

$router->dispatch();

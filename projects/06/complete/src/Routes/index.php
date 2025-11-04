<?php

use App\Controllers\Admin\UsersController; // new line
use App\Controllers\ProfileController; // new line
use App\Controllers\AuthController; //new line
use App\Controllers\HomeController;
use App\Controllers\ContactController;
use App\Router;

$router = new Router();

$router->get('/', HomeController::class, 'index');
$router->get('/contact', ContactController::class, 'show');
$router->post('/contact', ContactController::class, 'submit');

$router->get('/register', AuthController::class, 'showRegister'); // new line
$router->post('/register', AuthController::class, 'register'); // new line

$router->get('/login', AuthController::class, 'showLogin'); // new line
$router->post('/login', AuthController::class, 'login'); // new line
$router->post('/logout', AuthController::class, 'logout'); // new line

$router->get('/profile', ProfileController::class, 'show'); // new line
$router->get('/profile/edit', ProfileController::class, 'edit'); // new line
$router->post('/profile', ProfileController::class, 'update'); // new line
$router->post('/profile/password', ProfileController::class, 'changePassword'); // new line

$router->get('/admin/users', UsersController::class, 'index'); // new line
$router->get('/admin/users/create', UsersController::class, 'create'); // new line
$router->post('/admin/users', UsersController::class, 'store'); // new line
$router->get('/admin/users/{id}/edit', UsersController::class, 'edit'); // new line
$router->post('/admin/users/{id}', UsersController::class, 'update'); // new line
$router->post('/admin/users/{id}/role', UsersController::class, 'updateRole'); // new line
$router->post('/admin/users/{id}/deactivate', UsersController::class, 'deactivate'); // new line
$router->post('/admin/users/{id}/active', UsersController::class, 'updateActive');

$router->dispatch();

<?php

use App\Controllers\HomeController;
use App\Controllers\ContactController;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\UsersController;
use App\Router;

$router = new Router();

$router->get('/', HomeController::class, 'index');
$router->get('/contact', ContactController::class, 'show');
$router->post('/contact', ContactController::class, 'submit');

// Auth
$router->get('/login', AuthController::class, 'showLogin');
$router->post('/login', AuthController::class, 'login');
$router->get('/register', AuthController::class, 'showRegister');
$router->post('/register', AuthController::class, 'register');
$router->post('/logout', AuthController::class, 'logout');

// Profile
$router->get('/profile', ProfileController::class, 'show');
$router->get('/profile/edit', ProfileController::class, 'edit');
$router->post('/profile', ProfileController::class, 'update');
$router->post('/profile/password', ProfileController::class, 'updatePassword');

// Admin: Users
$router->get('/admin/users', UsersController::class, 'index');
$router->get('/admin/users/create', UsersController::class, 'create');
$router->post('/admin/users', UsersController::class, 'store');
$router->get('/admin/users/edit', UsersController::class, 'edit'); // expects ?id=123
$router->post('/admin/users/update', UsersController::class, 'update');
$router->post('/admin/users/delete', UsersController::class, 'destroy');

$router->dispatch();

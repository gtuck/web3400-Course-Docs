<?php

use App\Controllers\Admin\UsersController;
use App\Controllers\ProfileController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\ContactController;
use App\Controllers\PostsController;
use App\Controllers\Admin\PostsController as AdminPostsController;
use App\Controllers\PostEngagementController;
use App\Controllers\CommentsController;
use App\Controllers\Admin\CommentsController as AdminCommentsController;
use App\Controllers\Admin\DashboardController;
use App\Router;

$router = new Router();

$router->get('/', HomeController::class, 'index');
$router->get('/contact', ContactController::class, 'show');
$router->post('/contact', ContactController::class, 'submit');

$router->get('/register', AuthController::class, 'showRegister');
$router->post('/register', AuthController::class, 'register');

$router->get('/login', AuthController::class, 'showLogin');
$router->post('/login', AuthController::class, 'login');
$router->post('/logout', AuthController::class, 'logout');

$router->get('/profile', ProfileController::class, 'show');
$router->get('/profile/edit', ProfileController::class, 'edit');
$router->post('/profile', ProfileController::class, 'update');
$router->post('/profile/password', ProfileController::class, 'changePassword');

// Admin dashboard
$router->get('/admin/dashboard', DashboardController::class, 'index');

// Admin users
$router->get('/admin/users', UsersController::class, 'index');
$router->get('/admin/users/create', UsersController::class, 'create');
$router->post('/admin/users', UsersController::class, 'store');
$router->get('/admin/users/{id}/edit', UsersController::class, 'edit');
$router->post('/admin/users/{id}', UsersController::class, 'update');
$router->post('/admin/users/{id}/role', UsersController::class, 'updateRole');
$router->post('/admin/users/{id}/active', UsersController::class, 'updateActive');

// Public post show
$router->get('/posts/{slug}', PostsController::class, 'show');

// Engagement routes
$router->post('/posts/{id}/like', PostEngagementController::class, 'like');
$router->post('/posts/{id}/unlike', PostEngagementController::class, 'unlike');
$router->post('/posts/{id}/fav', PostEngagementController::class, 'fav');
$router->post('/posts/{id}/unfav', PostEngagementController::class, 'unfav');

// Public comments
$router->post('/posts/{slug}/comments', CommentsController::class, 'store');
$router->post('/comments/{id}/delete', CommentsController::class, 'destroy');

// Admin posts management
$router->get('/admin/posts', AdminPostsController::class, 'index');
$router->get('/admin/posts/create', AdminPostsController::class, 'create');
$router->post('/admin/posts', AdminPostsController::class, 'store');
$router->get('/admin/posts/{id}/edit', AdminPostsController::class, 'edit');
$router->post('/admin/posts/{id}', AdminPostsController::class, 'update');
$router->post('/admin/posts/{id}/publish', AdminPostsController::class, 'publish');
$router->post('/admin/posts/{id}/unpublish', AdminPostsController::class, 'unpublish');
$router->post('/admin/posts/{id}/delete', AdminPostsController::class, 'destroy');

// Admin comments moderation
$router->get('/admin/comments', AdminCommentsController::class, 'index');
$router->post('/admin/comments/{id}/publish', AdminCommentsController::class, 'publish');
$router->post('/admin/comments/{id}/delete', AdminCommentsController::class, 'destroy');

$router->dispatch();


<?php

use App\Controllers\Admin\UsersController; // new line
use App\Controllers\ProfileController; // new line
use App\Controllers\AuthController; //new line
use App\Controllers\HomeController;
use App\Controllers\ContactController;
use App\Controllers\PostsController;
use App\Controllers\Admin\PostsController as AdminPostsController;
use App\Controllers\PostEngagementController;
use App\Controllers\CommentsController;
use App\Controllers\Admin\CommentsController as AdminCommentsController;
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
$router->post('/admin/users/{id}/active', UsersController::class, 'updateActive');

$router->get('/posts/{slug}', PostsController::class, 'show');

// Engagement routes
$router->post('/posts/{id}/like', PostEngagementController::class, 'like');
$router->post('/posts/{id}/unlike', PostEngagementController::class, 'unlike');
$router->post('/posts/{id}/fav', PostEngagementController::class, 'fav');
$router->post('/posts/{id}/unfav', PostEngagementController::class, 'unfav');

// Public comments
$router->post('/posts/{slug}/comments', CommentsController::class, 'store');
$router->post('/comments/{id}/delete', CommentsController::class, 'destroy');

// Admin Posts management
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

<?php

namespace App;

/**
 * Router - Simple routing system for handling HTTP requests
 *
 * Maps URLs to controller actions based on the request method (GET, POST, etc.).
 * Provides a clean interface for defining application routes and dispatching
 * requests to the appropriate controller methods.
 *
 * Example usage:
 *
 * // Create router instance
 * $router = new Router();
 *
 * // Define GET routes
 * $router->get('/', 'App\Controllers\HomeController', 'index');
 * $router->get('/about', 'App\Controllers\HomeController', 'about');
 * $router->get('/users', 'App\Controllers\UserController', 'list');
 *
 * // Define POST routes
 * $router->post('/users/create', 'App\Controllers\UserController', 'create');
 * $router->post('/login', 'App\Controllers\AuthController', 'login');
 *
 * // Dispatch the current request
 * try {
 *     $router->dispatch();
 * } catch (\Exception $e) {
 *     // Handle 404 - route not found
 *     echo "404 Not Found: " . $e->getMessage();
 * }
 */
class Router
{
    /**
     * @var array Storage for registered routes, organized by HTTP method
     * Structure: ['GET' => ['/path' => ['controller' => ..., 'action' => ...]], 'POST' => [...]]
     */
    protected $routes = [];

    /**
     * Add a route to the routing table (private helper method)
     *
     * Internal method used by get(), post(), and other HTTP method helpers
     * to register routes. Stores the controller and action for a given path
     * and HTTP method combination.
     *
     * @param string $route The URL path to match (e.g., '/', '/users', '/profile/edit')
     * @param string $controller Fully qualified controller class name
     * @param string $action The method name to call on the controller
     * @param string $method HTTP method (GET, POST, PUT, DELETE, etc.)
     * @return void
     *
     * Note: This is a private method - use public methods like get() and post() instead.
     */
    private function addRoute($route, $controller, $action, $method)
    {
        $this->routes[$method][$route] = ['controller' => $controller, 'action' => $action];
    }

    /**
     * Register a GET route
     *
     * Defines a route that responds to HTTP GET requests. GET routes are typically
     * used for retrieving/displaying data (e.g., showing pages, listing resources).
     *
     * @param string $route The URL path to match (e.g., '/', '/users', '/profile')
     * @param string $controller Fully qualified controller class name
     * @param string $action The controller method to call when route matches
     * @return void
     *
     * Example:
     * // Homepage route
     * $router->get('/', 'App\Controllers\HomeController', 'index');
     *
     * // Display all users
     * $router->get('/users', 'App\Controllers\UserController', 'index');
     *
     * // Display user profile
     * $router->get('/profile', 'App\Controllers\UserController', 'profile');
     *
     * // About page
     * $router->get('/about', 'App\Controllers\HomeController', 'about');
     *
     * // Contact page
     * $router->get('/contact', 'App\Controllers\HomeController', 'contact');
     *
     * Note: Routes are exact matches - '/users' will not match '/users/123'
     */
    public function get($route, $controller, $action)
    {
        $this->addRoute($route, $controller, $action, 'GET');
    }

    /**
     * Register a POST route
     *
     * Defines a route that responds to HTTP POST requests. POST routes are typically
     * used for creating/submitting data (e.g., form submissions, creating resources).
     *
     * @param string $route The URL path to match (e.g., '/login', '/users/create')
     * @param string $controller Fully qualified controller class name
     * @param string $action The controller method to call when route matches
     * @return void
     *
     * Example:
     * // User login form submission
     * $router->post('/login', 'App\Controllers\AuthController', 'login');
     *
     * // User registration
     * $router->post('/register', 'App\Controllers\AuthController', 'register');
     *
     * // Create new user
     * $router->post('/users/create', 'App\Controllers\UserController', 'create');
     *
     * // Update user profile
     * $router->post('/profile/update', 'App\Controllers\UserController', 'update');
     *
     * // Delete user
     * $router->post('/users/delete', 'App\Controllers\UserController', 'delete');
     *
     * // Contact form submission
     * $router->post('/contact/send', 'App\Controllers\ContactController', 'send');
     *
     * Typical usage pattern:
     * // Display form (GET)
     * $router->get('/users/new', 'App\Controllers\UserController', 'new');
     * // Process form (POST)
     * $router->post('/users/create', 'App\Controllers\UserController', 'create');
     */
    public function post($route, $controller, $action)
    {
        $this->addRoute($route, $controller, $action, 'POST');
    }

    /**
     * Dispatch the current HTTP request to the appropriate controller
     *
     * Analyzes the incoming request URI and HTTP method, matches it against
     * registered routes, instantiates the appropriate controller, and calls
     * the specified action method.
     *
     * The URI is parsed to remove query strings, so '/users?page=2' becomes '/users'.
     * If no matching route is found, throws an exception (which can be caught to
     * display a 404 error page).
     *
     * @return void
     * @throws \Exception If no matching route is found for the current URI and method
     *
     * Example:
     * // In your main index.php file:
     * require_once 'vendor/autoload.php';
     *
     * $router = new Router();
     *
     * // Define routes
     * $router->get('/', 'App\Controllers\HomeController', 'index');
     * $router->get('/users', 'App\Controllers\UserController', 'index');
     * $router->post('/login', 'App\Controllers\AuthController', 'login');
     *
     * // Dispatch the request
     * try {
     *     $router->dispatch();
     * } catch (\Exception $e) {
     *     // Handle 404 errors
     *     http_response_code(404);
     *     echo "404 Not Found: " . htmlspecialchars($e->getMessage());
     * }
     *
     * How it works:
     * 1. User visits: http://example.com/users?page=2
     * 2. dispatch() extracts URI: '/users' (query string removed)
     * 3. Checks request method: 'GET'
     * 4. Looks up route: $routes['GET']['/users']
     * 5. Creates instance: new App\Controllers\UserController()
     * 6. Calls method: $controller->index()
     *
     * Error handling example:
     * // Custom 404 page
     * try {
     *     $router->dispatch();
     * } catch (\Exception $e) {
     *     $errorController = new App\Controllers\ErrorController();
     *     $errorController->notFound();
     * }
     */
    public function dispatch()
    {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($this->routes[$method][$uri])) {
            $controller = $this->routes[$method][$uri]['controller'];
            $action = $this->routes[$method][$uri]['action'];

            $controller = new $controller();
            $controller->$action();
        } else {
            throw new \Exception("No route found for URI: $uri");
        }
    }
}


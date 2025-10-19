<?php
/**
 * Router Class
 *
 * PURPOSE:
 * Handles HTTP request routing by mapping URLs to controller actions.
 * This is the central dispatcher that connects incoming requests to the appropriate
 * controller methods based on the HTTP method (GET, POST) and URL path.
 *
 * RESPONSIBILITIES:
 * - Register routes for different HTTP methods (GET, POST, etc.)
 * - Parse incoming request URI and HTTP method
 * - Match requests to registered routes
 * - Instantiate the appropriate controller
 * - Execute the specified action method
 *
 * USAGE EXAMPLE:
 * ```php
 * $router = new Router();
 * $router->get('/', HomeController::class, 'index');
 * $router->post('/contact', ContactController::class, 'submit');
 * $router->dispatch(); // Handles the current request
 * ```
 */

namespace App;

use App\Exceptions\RouteNotFoundException;

class Router
{
    /**
     * @var array Storage for registered routes organized by HTTP method
     * Structure: ['GET' => ['/path' => ['controller' => ClassName, 'action' => 'methodName']]]
     */
    protected $routes = [];

    /**
     * Internal method to register a route for a specific HTTP method
     *
     * @param string $route The URL path (e.g., '/', '/contact')
     * @param string $controller The fully-qualified controller class name
     * @param string $action The controller method name to execute
     * @param string $method The HTTP method (GET, POST, etc.)
     *
     * EXAMPLE:
     * $this->addRoute('/users', UserController::class, 'index', 'GET');
     */
    private function addRoute($route, $controller, $action, $method)
    {
        $this->routes[$method][$route] = ['controller' => $controller, 'action' => $action];
    }

    /**
     * Register a GET route
     *
     * Use this for routes that retrieve/display data (idempotent operations)
     *
     * @param string $route URL path
     * @param string $controller Controller class name
     * @param string $action Controller method name
     *
     * EXAMPLE:
     * $router->get('/about', PageController::class, 'about');
     * $router->get('/users', UserController::class, 'list');
     */
    public function get($route, $controller, $action)
    {
        $this->addRoute($route, $controller, $action, 'GET');
    }

    /**
     * Register a POST route
     *
     * Use this for routes that modify data (form submissions, etc.)
     *
     * @param string $route URL path
     * @param string $controller Controller class name
     * @param string $action Controller method name
     *
     * EXAMPLE:
     * $router->post('/contact', ContactController::class, 'submit');
     * $router->post('/users', UserController::class, 'create');
     */
    public function post($route, $controller, $action)
    {
        $this->addRoute($route, $controller, $action, 'POST');
    }

    /**
     * Register a PUT route
     *
     * Use this for routes that update existing resources
     *
     * @param string $route URL path
     * @param string $controller Controller class name
     * @param string $action Controller method name
     *
     * EXAMPLE:
     * $router->put('/users/1', UserController::class, 'update');
     *
     * NOTE: HTML forms only support GET/POST. For PUT requests, use method spoofing:
     * <form method="POST">
     *     <input type="hidden" name="_method" value="PUT">
     * </form>
     */
    public function put($route, $controller, $action)
    {
        $this->addRoute($route, $controller, $action, 'PUT');
    }

    /**
     * Register a DELETE route
     *
     * Use this for routes that delete resources
     *
     * @param string $route URL path
     * @param string $controller Controller class name
     * @param string $action Controller method name
     *
     * EXAMPLE:
     * $router->delete('/users/1', UserController::class, 'destroy');
     *
     * NOTE: HTML forms only support GET/POST. For DELETE requests, use method spoofing:
     * <form method="POST">
     *     <input type="hidden" name="_method" value="DELETE">
     * </form>
     */
    public function delete($route, $controller, $action)
    {
        $this->addRoute($route, $controller, $action, 'DELETE');
    }

    /**
     * Register a PATCH route
     *
     * Use this for routes that partially update resources
     *
     * @param string $route URL path
     * @param string $controller Controller class name
     * @param string $action Controller method name
     *
     * EXAMPLE:
     * $router->patch('/users/1', UserController::class, 'updatePartial');
     */
    public function patch($route, $controller, $action)
    {
        $this->addRoute($route, $controller, $action, 'PATCH');
    }

    /**
     * Dispatch the current request to the appropriate controller action
     *
     * PROCESS:
     * 1. Extract the URI path (strips query string with strtok)
     * 2. Get the HTTP method from $_SERVER (supports method spoofing)
     * 3. Look up the matching route
     * 4. Instantiate the controller using first-class callable syntax
     * 5. Call the action method on the controller
     *
     * METHOD SPOOFING:
     * Since HTML forms only support GET/POST, this method checks for a
     * hidden _method field to support PUT, DELETE, and PATCH requests.
     *
     * THROWS:
     * RouteNotFoundException if no matching route is found for the method/URI combination
     *
     * EXAMPLE FLOW:
     * Request: GET /contact
     * 1. $uri = '/contact', $method = 'GET'
     * 2. Finds route: ['controller' => ContactController::class, 'action' => 'show']
     * 3. Creates: new ContactController()
     * 4. Calls: $controller->show()
     */
    public function dispatch()
    {
        // Extract clean URI without query string
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        // Support method spoofing for PUT, DELETE, PATCH via _method field
        // This allows HTML forms to simulate PUT/DELETE requests
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        // Verify route exists
        if (!isset($this->routes[$method][$uri])) {
            throw new RouteNotFoundException($method, $uri);
        }

        // Instantiate controller and execute action
        $controller = new ($this->routes[$method][$uri]['controller']);
        $action = $this->routes[$method][$uri]['action'];
        $controller->$action();
    }
}

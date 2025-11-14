<?php

namespace App;

class Router
{
    protected $routes = [
        'GET' => [],
        'POST' => [],
    ];

    private function addRoute(string $route, string $controller, string $action, string $method): void
    {
        $paramNames = [];
        $pattern = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            function (array $m) use (&$paramNames): string {
                $paramNames[] = $m[1];
                return '(?P<' . $m[1] . '>[^/]+)';
            },
            $route
        );

        $this->routes[$method][] = [
            'pattern' => '#^' . $pattern . '$#',
            'controller' => $controller,
            'action' => $action,
            'params' => $paramNames,
        ];
    }

    public function get(string $route, string $controller, string $action): void
    {
        $this->addRoute($route, $controller, $action, 'GET');
    }

    public function post(string $route, string $controller, string $action): void
    {
        $this->addRoute($route, $controller, $action, 'POST');
    }

    public function dispatch(): void
    {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                $controller = new ($route['controller']);
                $params = array_map(fn(string $name) => $matches[$name] ?? null, $route['params']);
                $controller->{$route['action']}(...$params);
                return;
            }
        }

        throw new \Exception("No route for {$method} {$uri}");
    }
}

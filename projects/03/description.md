# Project 03 – MVC with Router, Namespaces, and Autoloading
Elevate your Project 02 refactor into a structured MVC app using a front controller, a simple router, namespaces, and PSR-4 autoloading.

---

## Overview
In Project 02, you separated concerns into Model, View, and Controller within a single folder. In Project 03, you will take the next step: organize your app into a small MVC structure that uses:

- A front controller (`public/index.php`)
- A lightweight router (`src/Router.php` + `src/Routes/index.php`)
- Namespaces and PSR-4 autoloading via Composer
- A base controller with a `render()` helper

You will keep the same blog posts feature and database logic, but restructure your code to match a scalable MVC layout like the provided files in `projects/03/files`.

---

## Learning Objectives
- Apply namespaces and PSR-4 autoloading in PHP
- Implement a front controller and a simple router
- Organize code into `Controllers`, `Models`, and `Views` with clear responsibilities
- Reuse your Project 02 logic in a more scalable architecture
- Maintain separation of concerns and PSR naming conventions

---

## Target File Structure
Your result should mirror the structure in `projects/03/files`:

```
projects/03/
  composer.json              # PSR-4 autoload (App\ => src/)
  public/
    index.php                # Front controller – loads autoload + routes
  src/
    Controller.php           # Base Controller with render()
    Router.php               # Minimal Router (GET/POST + dispatch)
    Controllers/
      HomeController.php     # Uses Blog model and renders view
    Models/
      Blog.php               # Fetches posts via PDO
    Routes/
      index.php              # Defines routes and dispatches
    Views/
      index.php              # HTML view for posts
  README.md                  # Basic run instructions
```

Your final app should respond to `GET /` by rendering the posts view.

---

## Step-by-Step Instructions

### 1) Set up Composer autoloading
Create `composer.json` in your project root with PSR-4 autoloading:

```json
{
  "name": "yourname/php-mvc",
  "autoload": {
    "psr-4": {
      "App\\": "src/"
    }
  },
  "require": {}
}
```

Then run:
```
composer dump-autoload
```

This generates `vendor/autoload.php` which will load namespaced classes automatically.

---

### 2) Create a front controller (public/index.php)
The front controller is the single entry point for all HTTP requests. It should load autoloading and then bootstrap routing:

```php
<?php
require '../vendor/autoload.php';

$router = require '../src/Routes/index.php';
```

Note: The routes file will create and dispatch the router; assigning the return value isn’t required, but this pattern keeps the bootstrapping flow clear.

---

### 3) Build a minimal Router (src/Router.php)
Implement a simple router that supports `GET` and `POST`, stores route mappings, and dispatches based on the current request URI and method.

Key requirements:
- `get($route, $controller, $action)` and `post($route, $controller, $action)` methods
- `dispatch()` that:
  - Parses the request URI (ignore query string)
  - Looks up the route by HTTP method
  - Instantiates the controller and calls the action
  - Throws a clear exception for unknown routes

```php
<?php
class Router {
  protected $routes = [];

  private function addRoute($route, $controller, $action, $method) {
    $this->routes[$method][$route] = ['controller' => $controller, 'action' => $action];
  }

  public function get($route, $controller, $action)  { $this->addRoute($route, $controller, $action, 'GET'); }
  public function post($route, $controller, $action) { $this->addRoute($route, $controller, $action, 'POST'); }

  public function dispatch() {
    $uri = strtok($_SERVER['REQUEST_URI'], '?');
    $method = $_SERVER['REQUEST_METHOD'];
    if (!isset($this->routes[$method][$uri])) throw new \Exception("No route for $method $uri");
    $controller = new ($this->routes[$method][$uri]['controller']);
    $action = $this->routes[$method][$uri]['action'];
    $controller->$action();
  }
}
```
---

### 4) Define routes (src/Routes/index.php)
Create the router, register routes, and dispatch:

```php
<?php
use App\Controllers\HomeController;
use App\Router;

$router = new Router();

$router->get('/', HomeController::class, 'index');

$router->dispatch();
```

For now, one route is enough: `/` → `HomeController@index`.

---

### 5) Base Controller and render helper (src/Controller.php)
Add a base controller with a `render($view, $data = [])` method:

```php
<?php
namespace App;

class Controller
{
    protected function render($view, $data = [])
    {
        extract($data);
        include "Views/$view.php";
    }
}
```

This keeps view rendering consistent and simple across controllers.

---

### 6) Model (src/Models/Blog.php)
Move your Project 02 database code into a namespaced `Blog` model. Keep PDO error mode and fetch mode settings:

```php
<?php
namespace App\Models;

class Blog
{
    public function getPosts(): array
    {
        $host = 'db';
        $dbname = 'web3400';
        $username = 'web3400';
        $password = 'password';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=UTF8";
        $pdo = new \PDO($dsn, $username, $password, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);

        $stmt = $pdo->query("SELECT * FROM posts");
        return $stmt->fetchAll();
    }
}
```

---

### 7) Controller (src/Controllers/HomeController.php)
Create `HomeController` that extends the base controller, fetches posts from `Blog`, and renders the `index` view with data:

```php
<?php
namespace App\Controllers;

use App\Controller;
use App\Models\Blog;

class HomeController extends Controller
{
    public function index()
    {
        $blog = new Blog();
        $posts = $blog->getPosts();
        $this->render('index', ['posts' => $posts]);
    }
}
```

---

### 8) View (src/Views/index.php)
Reuse and relocate your Project 02 `view.php` HTML into `src/Views/index.php`:

```php
<!DOCTYPE html>
<html>
<head>
    <title>Blog Posts</title>
    <meta charset="UTF-8">
</head>
<body>

<h1>Blog Posts</h1>

<?php foreach ($posts as $post): ?>

    <h2><?= htmlspecialchars($post["title"]) ?></h2>
    <p><?= htmlspecialchars($post["body"]) ?></p>

<?php endforeach; ?>

</body>
</html>
```

Ensure all dynamic output uses `htmlspecialchars()`.

---

## Run and Test
From `projects/03/`:

```
composer dump-autoload
php -S 0.0.0.0:8000 -t public
```

Open `http://localhost:8000/` and verify that posts render with the same data as Project 02.

If running the built-in server from the repo root, you can also do:

```
php -S 0.0.0.0:8000 -t projects/03/public
```

---

## Tips, Standards, and Gotchas
- Use namespaces consistently and match them to folders (`App\Controllers\HomeController` → `src/Controllers/HomeController.php`).
- Keep PSR naming: classes `StudlyCaps`, methods `camelCase`.
- Keep Controller thin: orchestrate models and views; don’t embed DB logic.
- Keep View focused on display; escape all output.
- Router should parse the path without query string and check both URI and HTTP method.
- If you see “Class not found”, run `composer dump-autoload` again.

---

## Submission
Submit the direct URL to your Project 03 folder in your repository (replace YOUR-USER and repo name):

```
https://github.com/YOUR-USER/YOUR-REPO/blob/main/projects/03/
```

Ensure your code structure and runtime behavior matches the files in [`projects/03/files`](files).

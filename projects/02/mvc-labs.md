# Labs: Build a Micro MVC (Project 02)

These labs focus on setting up a tiny MVC framework inside `projects/02/php-micro-mvc` and rendering a simple homepage (no database or user features yet). Use the `<head>` markup from Project 01 for the view layout. Do not add any custom styles beyond Bulma, FontAwesome, and BulmaJS.

Time guidance per lab: 20–40 minutes.

---

## Lab 1 — Front Controller + Router

Goal: Send all requests through `public/index.php` and dispatch based on method + path.

1) Create structure and bootstrap

```
php-micro-mvc/
  public/index.php
  src/Router.php
  src/Routes/index.php
  composer.json (PSR-4 autoload App\\ → src/)
```

Quick scaffold command (macOS/Linux)

```bash
mkdir -p public src/Routes src/Controllers src/Views \
  && touch public/index.php src/Router.php src/Routes/index.php
```

composer.json (create or update):

```json
{
  "autoload": {
    "psr-4": { "App\\": "src/" }
  }
}
```

Regenerate autoload files (run from `php-micro-mvc/`):

```bash
composer dump-autoload
```

2) Router

```php
<?php // src/Router.php
namespace App;

class Router
{
    protected $routes = [];

    private function addRoute($route, $controller, $action, $method)
    {
        $this->routes[$method][$route] = ['controller' => $controller, 'action' => $action];
    }

    public function get($route, $controller, $action)  { $this->addRoute($route, $controller, $action, 'GET'); }
    public function post($route, $controller, $action) { $this->addRoute($route, $controller, $action, 'POST'); }

    public function dispatch()
    {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];
        if (isset($this->routes[$method][$uri])) {
            $c = $this->routes[$method][$uri]['controller'];
            $m = $this->routes[$method][$uri]['action'];
            $controller = new $c();
            $controller->$m();
        } else {
            // Temporary 404; Lab 3 adds a proper view
            http_response_code(404);
            echo '<h1>404</h1>';
        }
    }
}
```

3) public/index.php

```php
<?php
// public/index.php
require '../vendor/autoload.php';
$router = require '../src/Routes/index.php';
```

Acceptance criteria
- [ ] All requests go through `public/index.php`
- [ ] `GET /` responds 200 (even if placeholder)
- [ ] Unknown routes return 404

---

## Lab 2 — Controller + View + Layout

Goal: Render HTML via views and a base layout that reuses the Project 01 `<head>` markup.

1) Base Controller with layout render

```php
<?php // src/Controller.php
namespace App;
class Controller {
  protected function render(string $view, array $data=[]): void {
    extract($data, EXTR_SKIP);
    ob_start();
    include __DIR__ . "/Views/$view.php";
    $content = ob_get_clean();
    include __DIR__ . "/Views/layout.php";
  }
}
```

2) Home controller + views

```php
<?php // src/Controllers/HomeController.php
namespace App\Controllers;
use App\Controller;
class HomeController extends Controller {
  public function index() { $this->render('index', ['title'=>'Home - Micro MVC']); }
}
```

```php
<!-- src/Views/layout.php -->
<?php $pageTitle = ($title ?? 'Site Title'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Project 02 - Micro MVC">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></title>

  <!-- Bulma & Assets (match Project 01) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
  <script src="https://cdn.jsdelivr.net/npm/@vizuaalog/bulmajs@0.12/dist/bulma.min.js" defer></script>
</head>
<body class="has-navbar-fixed-top">
  <main class="container">
    <?= $content ?? '' ?>
  </main>
  <footer class="footer">
    <div class="content has-text-centered">
      <p>&copy; <?= date('Y') ?> — <?= htmlspecialchars(($siteName ?? 'My PHP Site'), ENT_QUOTES) ?></p>
    </div>
  </footer>
</body>
</html>
```

```php
<!-- src/Views/index.php -->
<?php $title = 'Home - Micro MVC'; ?>

<!-- Hero (match the style of Project 00 index.php, but minimal content) -->
<section class="hero is-primary">
  <div class="hero-body">
    <p class="title">Hero title</p>
    <p class="subtitle">Hero subtitle</p>
  </div>
</section>

<!-- Page content -->
<section class="section">
  <h1 class="title">Welcome to Micro MVC</h1>
  <h2 class="subtitle">This is a simple homepage rendered via Controller → View → Layout.</h2>
</section>
```

Acceptance criteria
- [ ] `GET /` renders via `HomeController@index` and uses the layout
- [ ] No HTML is printed directly from controllers

---
## Lab 3 — 404 View (Optional Polish)

Goal: Replace the inline 404 HTML with a proper view template to keep controllers slim and consistent.

1) Create a 404 view

```php
<!-- src/Views/errors/404.php -->
<?php $title = 'Not Found'; ?>
<section class="section">
  <h1 class="title">404 — Page Not Found</h1>
  <p class="subtitle">The page you requested could not be found.</p>
  <p><a class="button" href="/">Go Home</a></p>
  </section>
```

2) Update Router to use the 404 view

```php
// src/Router.php (inside dispatch)
if (!isset($this->routes[$method][$uri])) {
  // Render 404 through layout
  ob_start();
  $title = 'Not Found';
  include __DIR__ . '/Views/errors/404.php';
  $content = ob_get_clean();
  ob_start(); include __DIR__ . '/Views/layout.php';
  $body = ob_get_clean();
  http_response_code(404);
  echo $body;
  return;
}
```

Acceptance criteria
- [ ] Unknown routes return a styled 404 page through the layout

---

## Optional Enhancements
- Add a minimal `nav` partial and include it in the layout if desired
- Add a simple `flash` helper and section in the layout for future PRG use
- Centralize site name in a config array and pass it to the view data
- Windows: provide a PowerShell scaffold and use `php -S localhost:9999 -t public`

---

## Deliverables (Project 02)
- Link to `projects/02/php-micro-mvc` that contains your micro MVC (front controller, router, controller, view, layout)
- A simple homepage rendered via `HomeController::index`
- README with run steps: `composer dump-autoload` and `php -S localhost:9999 -t public`

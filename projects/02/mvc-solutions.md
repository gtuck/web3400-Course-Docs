# Instructor Notes & Reference Solutions — Project 02 MVC

Use this as a teaching aid and for preparing your live‑coding. Share selectively with students (avoid handing out the full file before the lab).

---

## Suggested Flow (45–60 minutes)
- 5m: Recap PRG; why MVC; request lifecycle diagram
- 10m: Scaffold `php-micro-mvc/` and composer autoload; front controller vs multi‑script
- 15m: Live‑code Router + Routes file; add `GET /`
- 15m: Live‑code Controller + View + Layout; render Home using Project 01 head/footer
- 5–10m: Optional 404 view wired through the layout

Tips
- Keep each class < 50 lines in live‑coding; move faster by copy/paste from snippets
- Emphasize that PRG still applies in MVC — placement is in controllers

Pitfalls to call out
- Headers already sent (echo before redirect)
- Printing HTML in controllers instead of returning a view

---

## Reference Implementation (Minimal, `projects/02/php-micro-mvc`)

### composer.json
```json
{
  "autoload": { "psr-4": { "App\\": "src/" } }
}
```

Run `composer dump-autoload` in the folder.

### Core
```php
<?php // src/Router.php
namespace App;
class Router {
  protected $routes = [];
  private function addRoute($route,$controller,$action,$method){ $this->routes[$method][$route] = ['controller'=>$controller,'action'=>$action]; }
  public function get($r,$c,$a){ $this->addRoute($r,$c,$a,'GET'); }
  public function post($r,$c,$a){ $this->addRoute($r,$c,$a,'POST'); }
  public function dispatch(){
    $uri = strtok($_SERVER['REQUEST_URI'],'?'); $method = $_SERVER['REQUEST_METHOD'];
    if (isset($this->routes[$method][$uri])) { $c=$this->routes[$method][$uri]['controller']; $m=$this->routes[$method][$uri]['action']; (new $c())->$m(); }
    else { http_response_code(404); echo '<h1>404</h1>'; }
  }
}
```

```php
<?php // src/Controller.php
namespace App;
class Controller {
  protected function render(string $view, array $data=[]): void {
    extract($data, EXTR_SKIP);
    ob_start(); include __DIR__ . "/Views/$view.php"; $content = ob_get_clean();
    include __DIR__ . "/Views/layout.php";
  }
}
```

### Routes + Controllers
```php
<?php // src/Routes/index.php
use App\Router; use App\Controllers\HomeController;
$router = new Router();
$router->get('/', HomeController::class, 'index');
$router->dispatch();
return $router;
```

```php
<?php // src/Controllers/HomeController.php
namespace App\Controllers; use App\Controller;
class HomeController extends Controller { public function index(){ $this->render('index',['title'=>'Home - Micro MVC']); } }
```

### Views
```php
<!-- src/Views/layout.php -->
<?php $pageTitle = $pageTitle ?? ($title ?? 'Site Title'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="description" content="Project 02 - Micro MVC">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
  <script src="https://cdn.jsdelivr.net/npm/@vizuaalog/bulmajs@0.12/dist/bulma.min.js" defer></script>
</head>
<body class="has-navbar-fixed-top">
  <main class="container"> <?= $content ?? '' ?> </main>
  <footer class="footer"><div class="content has-text-centered"><p>&copy; <?= date('Y') ?> — <?= htmlspecialchars(($siteName ?? 'My PHP Site'), ENT_QUOTES) ?></p></div></footer>
</body></html>
```

```php
<!-- src/Views/index.php -->
<?php $title='Home - Micro MVC'; ?>
<section class="hero is-primary"><div class="hero-body"><p class="title">Hero title</p><p class="subtitle">Hero subtitle</p></div></section>
<section class="section"><h1 class="title">Welcome to Micro MVC</h1><h2 class="subtitle">Homepage rendered via Controller → View → Layout</h2></section>
```

### public/index.php
```php
<?php
require '../vendor/autoload.php';
$router = require '../src/Routes/index.php';
```

### Optional: 404 view
```php
<!-- src/Views/errors/404.php -->
<?php $title = 'Not Found'; ?>
<section class="section">
  <h1 class="title">404 — Page Not Found</h1>
  <p class="subtitle">The page you requested could not be found.</p>
  <p><a class="button" href="/">Go Home</a></p>
</section>
```

To use it, render via the layout from `Router::dispatch()` when a route is missing (same pattern as the lab).

---

## Talking Points for Assessment
- Students can articulate how PRG works inside controllers
- Routes map predictably to controller actions
- Views are free of DB calls and heavy logic
- No custom CSS beyond Bulma/FontAwesome/BulmaJS; layout mirrors Project 01 head


# Labs: Build a Micro MVC (Project 02)

These labs guide you from a PRG script‑based app to a micro MVC framework you build yourself. Work inside a fresh folder (copy your Project 01 files if needed) and refactor progressively.

Time guidance per lab: 30–60 minutes.

---

## Lab 1 — Front Controller + Router

Goal: Send all requests through `public/index.php` and dispatch based on method + path.

1) Create structure and bootstrap

```
project-root/
  public/index.php
  app/Core/{Router.php,Request.php,Response.php}
  bootstrap.php
  config.php
```

2) config.php

```php
<?php
// config.php
return [
  'dsn'  => 'mysql:host=127.0.0.1;dbname=web3400;charset=utf8mb4',
  'user' => 'root',
  'pass' => 'root',
];
```

3) bootstrap.php

```php
<?php
// bootstrap.php
spl_autoload_register(function($class) {
    $prefix = 'App\\';
    if (str_starts_with($class, $prefix)) {
        $path = __DIR__ . '/app/' . str_replace('App\\', '', $class) . '.php';
        $path = str_replace('\\', '/', $path);
        if (file_exists($path)) require $path;
    }
});

session_start();
```

4) Core classes (starter)

```php
<?php // app/Core/Request.php
namespace App\Core;
class Request {
  public string $method; public string $path; public array $get; public array $post; public array $server;
  public static function capture(): self {
    $r = new self();
    $r->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    $r->path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $r->get = $_GET; $r->post = $_POST; $r->server = $_SERVER; return $r;
  }
  public function input(string $key, $default=null){ return $this->post[$key] ?? $this->get[$key] ?? $default; }
  public function only(array $keys): array { $out=[]; foreach($keys as $k){ $out[$k]=$this->input($k); } return $out; }
}
```

```php
<?php // app/Core/Response.php
namespace App\Core;
class Response {
  public function __construct(private int $status=200, private array $headers=[], private string $body=''){}
  public static function html(string $body, int $status=200): self { return new self($status, ['Content-Type'=>'text/html; charset=utf-8'],$body); }
  public static function redirect(string $to, int $status=302): self { return new self($status, ['Location'=>$to], ''); }
  public function send(): void { http_response_code($this->status); foreach($this->headers as $k=>$v) header($k.': '.$v); echo $this->body; }
}
```

```php
<?php // app/Core/Router.php
namespace App\Core;

class Router {
  private array $routes = ['GET'=>[], 'POST'=>[]];
  public function get(string $path, string $action): void { $this->routes['GET'][$path] = $action; }
  public function post(string $path, string $action): void { $this->routes['POST'][$path] = $action; }
  public function dispatch(Request $req): Response {
    $action = $this->routes[$req->method][$req->path] ?? null;
    if (!$action) return Response::html('<h1>404</h1>', 404);
    [$controller,$method] = explode('@', $action);
    $fqcn = 'App\\Controllers\\'.$controller;
    $instance = new $fqcn();
    return $instance->$method($req);
  }
}
```

5) public/index.php

```php
<?php
require __DIR__ . '/../bootstrap.php';

use App\Core\{Router, Request};

$router = new Router();
$router->get('/', 'HomeController@index');

$request = Request::capture();
$router->dispatch($request)->send();
```

Acceptance criteria
- [ ] All requests go through `public/index.php`
- [ ] `GET /` responds 200 (even if placeholder)
- [ ] Unknown routes return 404

---

## Lab 2 — Controllers + Views + Layout

Goal: Render HTML via views and a base layout.

1) Base Controller + View renderer

```php
<?php // app/Core/Controller.php
namespace App\Core;
class Controller {
  protected function view(string $name, array $data=[]): Response {
    return View::make($name, $data);
  }
  protected function redirect(string $to): Response { return Response::redirect($to); }
}
```

```php
<?php // app/Core/View.php
namespace App\Core;

class View {
  public static function make(string $name, array $data=[]): Response {
    $viewPath = __DIR__ . '/../Views/' . $name . '.php';
    extract($data, EXTR_SKIP);
    ob_start();
    include $viewPath; // sets $content or captures direct output
    $content = ob_get_clean();
    ob_start();
    include __DIR__ . '/../Views/layout.php';
    return Response::html(ob_get_clean());
  }
}
```

2) Home controller + views

```php
<?php // app/Controllers/HomeController.php
namespace App\Controllers;
use App\Core\{Controller, Request, Response};

class HomeController extends Controller {
  public function index(Request $r): Response {
    return $this->view('home/index', ['title' => 'Welcome']);
  }
}
```

```php
<!-- app/Views/layout.php -->
<!doctype html>
<html><head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= $title ?? 'App' ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.0/css/bulma.min.css">
  <style> body { max-width: 900px; margin: 2rem auto; } </style>
</head><body>
  <nav><a href="/">Home</a> · <a href="/users">Users</a></nav><hr>
  <main><?= $content ?? '' ?></main>
</body></html>
```

```php
<!-- app/Views/home/index.php -->
<?php $title = 'Welcome'; ?>
<h1 class="title">Micro MVC</h1>
<p class="subtitle">Your first controller + view</p>
```

Acceptance criteria
- [ ] `GET /` renders via `HomeController@index` and uses the layout
- [ ] No HTML is printed directly from controllers

---

## Lab 3 — Database + Model

Goal: Move DB code into a model and render data in a view.

1) Database helper

```php
<?php // app/Core/Database.php
namespace App\Core; use PDO;
class Database {
  public static function pdo(): PDO {
    static $pdo; if ($pdo) return $pdo;
    $cfg = require __DIR__.'/../../config.php';
    return $pdo = new PDO($cfg['dsn'],$cfg['user'],$cfg['pass'],[
      PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    ]);
  }
}
```

2) Model

```php
<?php // app/Models/User.php
namespace App\Models; use App\Core\Database;
class User {
  public static function all(): array { return Database::pdo()->query('SELECT * FROM users ORDER BY id DESC')->fetchAll(); }
  public static function create(array $data): void {
    $stmt = Database::pdo()->prepare('INSERT INTO users(full_name,email,phone) VALUES (?,?,?)');
    $stmt->execute([$data['full_name'],$data['email'],$data['phone']]);
  }
}
```

3) Users controller + views

```php
<?php // app/Controllers/UsersController.php
namespace App\Controllers; use App\Core\{Controller, Request, Response}; use App\Models\User;
class UsersController extends Controller {
  public function index(Request $r): Response { return $this->view('users/index', ['users'=>User::all()]); }
  public function create(Request $r): Response { return $this->view('users/create'); }
  public function store(Request $r): Response { User::create($r->only(['full_name','email','phone'])); return $this->redirect('/users'); }
}
```

```php
<!-- app/Views/users/index.php -->
<?php $title='Users'; ?>
<h1 class="title">Users</h1>
<p><a class="button is-link" href="/users/create">New User</a></p>
<ul>
  <?php foreach($users as $u): ?>
    <li><?= htmlspecialchars($u['full_name']) ?> (<?= htmlspecialchars($u['email']) ?>)</li>
  <?php endforeach; ?>
  <?php if (empty($users)): ?><li>No users yet.</li><?php endif; ?>
</ul>
```

```php
<!-- app/Views/users/create.php -->
<?php $title='Create User'; ?>
<h1 class="title">Create User</h1>
<form action="/users" method="post" class="box">
  <div class="field"><label class="label">Full Name</label><div class="control"><input class="input" name="full_name" required></div></div>
  <div class="field"><label class="label">Email</label><div class="control"><input class="input" type="email" name="email" required></div></div>
  <div class="field"><label class="label">Phone</label><div class="control"><input class="input" type="tel" name="phone"></div></div>
  <div class="field is-grouped"><div class="control"><button class="button is-link">Save</button></div><div class="control"><a class="button" href="/users">Cancel</a></div></div>
  <input type="hidden" name="_token" value="<?= $_SESSION['_token'] ?? '' ?>">
  </form>
```

4) Register routes in `public/index.php`

```php
$router->get('/users', 'UsersController@index');
$router->get('/users/create', 'UsersController@create');
$router->post('/users', 'UsersController@store');
```

Acceptance criteria
- [ ] Users index lists DB rows
- [ ] Create form inserts a row and redirects (PRG)

---

## Lab 4 — PRG + Flash + Basic Validation

Goal: After POST, redirect and show a success message on GET.

1) Flash helper

```php
<?php // app/Core/Flash.php
namespace App\Core;
class Flash {
  public static function set(string $key, string $message): void { $_SESSION['flash'][$key] = $message; }
  public static function get(string $key): ?string { $m = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $m; }
}
```

2) Use in controller

```php
use App\Core\Flash;
public function store(Request $r): Response {
  $data = $r->only(['full_name','email','phone']);
  // very basic validation
  if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    Flash::set('error', 'Invalid email');
    return $this->redirect('/users/create');
  }
  User::create($data);
  Flash::set('success', 'User created');
  return $this->redirect('/users');
}
```

3) Show in layout

```php
<?php if ($msg = App\Core\Flash::get('success')): ?>
  <div class="notification is-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
<?php if ($msg = App\Core\Flash::get('error')): ?>
  <div class="notification is-danger"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
```

Acceptance criteria
- [ ] POST actions redirect, not render
- [ ] Success/error messages appear once after redirect
- [ ] Basic validation prevents bad inserts

---

## Optional Enhancements
- CSRF token: generate on session start, include in forms, verify in POST
- 404 view: replace inline 404 HTML with a proper view template
- Route params: `/users/{id}` pattern matching
- Middleware: functions that run before a route (e.g., auth check)

---

## Deliverables (Project 02)
- Link to your Project 02 folder that contains your micro MVC implementation
- Short README describing what you implemented and how to run locally
- Screenshots or screencast showing PRG in action (create, redirect, flash)


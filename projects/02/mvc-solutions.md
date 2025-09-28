# Instructor Notes & Reference Solutions — Project 02 MVC

Use this as a teaching aid and for preparing your live‑coding. Share selectively with students (avoid handing out the full file before the lab).

---

## Suggested Flow (75–90 minutes)
- 5m: Recap PRG; why MVC; request lifecycle diagram
- 10m: Scaffold folders; explain front controller vs multi‑script
- 15m: Live‑code Router + Request + Response; add `GET /`
- 15m: Live‑code Controller + View + Layout; render Home
- 20m: Live‑code Database + Model + UsersController; list and create
- 10m: Add PRG + Flash; show one‑time success message

Tips
- Keep each class < 50 lines in live‑coding; move faster by copy/paste from snippets
- Seed `users` table before class to ensure list view shows data
- Emphasize that PRG still applies in MVC — the difference is placement (controller)

Pitfalls to call out
- Headers already sent (echo before redirect)
- Direct DB calls in views
- Not clearing flash after reading

---

## Reference Implementation (Minimal)

Note: Namespaces assume `App\...` resolved to `app/` with the autoloader in `bootstrap.php`.

### bootstrap.php
```php
<?php
spl_autoload_register(function($class) {
  $prefix = 'App\\';
  if (str_starts_with($class, $prefix)) {
    $path = __DIR__.'/app/'.str_replace('App\\','',$class).'.php';
    $path = str_replace('\\','/',$path);
    if (file_exists($path)) require $path;
  }
});
session_start();
```

### config.php
```php
<?php
return [
  'dsn'  => 'mysql:host=127.0.0.1;dbname=web3400;charset=utf8mb4',
  'user' => 'root',
  'pass' => 'root',
];
```

### Core
```php
<?php // app/Core/Request.php
namespace App\Core;
class Request {
  public string $method; public string $path; public array $get; public array $post;
  public static function capture(): self {
    $r = new self();
    $r->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    $r->path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $r->get = $_GET; $r->post = $_POST; return $r;
  }
  public function input(string $key, $default=null){ return $this->post[$key] ?? $this->get[$key] ?? $default; }
  public function only(array $keys): array { $o=[]; foreach($keys as $k){ $o[$k]=$this->input($k); } return $o; }
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
  public function get(string $p, string $a): void { $this->routes['GET'][$p]=$a; }
  public function post(string $p, string $a): void { $this->routes['POST'][$p]=$a; }
  public function dispatch(Request $r): Response {
    $a = $this->routes[$r->method][$r->path] ?? null;
    if (!$a) return Response::html('<h1>404</h1>',404);
    [$c,$m] = explode('@',$a); $fqcn='App\\Controllers\\'.$c; $o=new $fqcn(); return $o->$m($r);
  }
}
```

```php
<?php // app/Core/Controller.php
namespace App\Core;
class Controller {
  protected function view(string $name, array $data=[]): Response { return View::make($name, $data); }
  protected function redirect(string $to): Response { return Response::redirect($to); }
}
```

```php
<?php // app/Core/View.php
namespace App\Core;
class View {
  public static function make(string $name, array $data=[]): Response {
    $view = __DIR__.'/../Views/'.$name.'.php'; extract($data, EXTR_SKIP);
    ob_start(); include $view; $content = ob_get_clean();
    ob_start(); include __DIR__.'/../Views/layout.php'; $html = ob_get_clean();
    return Response::html($html);
  }
}
```

```php
<?php // app/Core/Database.php
namespace App\Core; use PDO;
class Database {
  public static function pdo(): PDO {
    static $pdo; if ($pdo) return $pdo;
    $c = require __DIR__.'/../../config.php';
    return $pdo = new PDO($c['dsn'],$c['user'],$c['pass'],[
      PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    ]);
  }
}
```

```php
<?php // app/Core/Flash.php
namespace App\Core;
class Flash {
  public static function set(string $key, string $msg): void { $_SESSION['flash'][$key]=$msg; }
  public static function get(string $key): ?string { $m=$_SESSION['flash'][$key]??null; unset($_SESSION['flash'][$key]); return $m; }
}
```

### Controllers
```php
<?php // app/Controllers/HomeController.php
namespace App\Controllers; use App\Core\{Controller, Request, Response};
class HomeController extends Controller { public function index(Request $r): Response { return $this->view('home/index',['title'=>'Welcome']); } }
```

```php
<?php // app/Controllers/UsersController.php
namespace App\Controllers; use App\Core\{Controller, Request, Response, Flash}; use App\Models\User;
class UsersController extends Controller {
  public function index(Request $r): Response { return $this->view('users/index',['users'=>User::all()]); }
  public function create(Request $r): Response { return $this->view('users/create'); }
  public function store(Request $r): Response {
    $d = $r->only(['full_name','email','phone']);
    if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) { Flash::set('error','Invalid email'); return $this->redirect('/users/create'); }
    User::create($d); Flash::set('success','User created'); return $this->redirect('/users');
  }
}
```

### Model
```php
<?php // app/Models/User.php
namespace App\Models; use App\Core\Database;
class User {
  public static function all(): array { return Database::pdo()->query('SELECT * FROM users ORDER BY id DESC')->fetchAll(); }
  public static function create(array $d): void { $s=Database::pdo()->prepare('INSERT INTO users(full_name,email,phone) VALUES (?,?,?)'); $s->execute([$d['full_name'],$d['email'],$d['phone']]); }
}
```

### Views
```php
<!-- app/Views/layout.php -->
<!doctype html><html><head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= $title ?? 'App' ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.0/css/bulma.min.css">
  <style> body { max-width: 900px; margin: 2rem auto; } </style>
</head><body>
  <nav><a href="/">Home</a> · <a href="/users">Users</a></nav><hr>
  <?php if ($m = App\Core\Flash::get('success')): ?><div class="notification is-success"><?= htmlspecialchars($m) ?></div><?php endif; ?>
  <?php if ($m = App\Core\Flash::get('error')):   ?><div class="notification is-danger"><?= htmlspecialchars($m) ?></div><?php endif; ?>
  <main><?= $content ?? '' ?></main>
</body></html>
```

```php
<!-- app/Views/home/index.php -->
<?php $title='Welcome'; ?>
<h1 class="title">Micro MVC</h1>
<p class="subtitle">Home page</p>
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
</form>
```

### public/index.php
```php
<?php
require __DIR__.'/../bootstrap.php';
use App\Core\{Router, Request};
$router = new Router();
$router->get('/', 'HomeController@index');
$router->get('/users', 'UsersController@index');
$router->get('/users/create', 'UsersController@create');
$router->post('/users', 'UsersController@store');
$router->dispatch(Request::capture())->send();
```

---

## Talking Points for Assessment
- Students can articulate how PRG works inside controllers
- Routes map predictably to controller actions
- Views are free of DB calls and heavy logic
- POST actions end with redirect and surface flash message on GET


# Project 02 Lecture: From PRG to MVC — Build a Micro MVC in PHP

## Learning Objectives
- Explain PRG (Post‑Redirect‑Get) and why it’s useful.
- Describe the MVC pattern and responsibilities of Model, View, Controller.
- Trace the request lifecycle in a front‑controller MVC.
- Build core pieces of a micro MVC: router, controller base, view renderer.
- Apply PRG inside controllers with redirects and flash messages.
- Map an existing PRG app into MVC routes, controllers, views, and models.

---

## Context: From PRG Scripts to MVC
Many of your Project 01 pages mixed several concerns: reading input, running database queries, deciding where to go next, and rendering HTML. The PRG pattern helped with forms: after handling a POST, you redirected to a GET to avoid duplicate submissions.

Example PRG flow in a script‑based app:

```php
// register.php (script-based)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // validate + insert user
    // redirect after success (PRG)
    header('Location: /login.php?registered=1');
    exit; 
}

// render registration form (GET)
include 'templates/head.php';
// ... form HTML ...
```

MVC keeps PRG, but moves logic into a Controller. Views render HTML. Models handle data. A single front controller (public/index.php) receives every request, routes it to the right controller action, and returns a Response.

---

## MVC In One Slide
- Model: Data + business rules. Talks to the database (via PDO) and returns domain objects/data.
- View: Presentation. Knows how to render data into HTML using simple templates/partials.
- Controller: Orchestration. Reads Request, calls Model, selects View, returns Response. Implements PRG by redirecting after POST.

Key benefits:
- Separation of concerns (easier to test, change, and extend)
- Predictable request lifecycle and routing
- Clear place to enforce validation, authorization, and PRG

---

## Request Lifecycle (Micro MVC)
1. Browser sends HTTP request to `public/index.php` (front controller)
2. Index bootstraps autoloading, config, session
3. Router matches method + path to a controller action (e.g., `UsersController@store`)
4. Controller reads input, calls models; on success for POST, redirects (PRG)
5. Controller returns a view (GET) or a redirect response (POST)
6. View templates render HTML using a base layout and sections

ASCII flow:

```
Request -> public/index.php -> Router -> Controller -> Model -> View -> Response
                              (POST success -> Redirect -> GET -> View)
```

---

## Minimal Project Structure
This structure is small, explicit, and framework‑free.

```
project-root/
  public/
    index.php                # front controller
  app/
    Core/
      Router.php            # method+path routing
      Request.php           # wraps globals (GET/POST/SESSION)
      Response.php          # send headers/body, redirects
      Controller.php        # base controller with view()/redirect()
      View.php              # simple layout + partial rendering
      Database.php          # returns configured PDO
      Flash.php             # PRG-friendly session flash messages
    Controllers/
      HomeController.php
      UsersController.php
    Models/
      User.php
    Views/
      layout.php
      home/index.php
      users/index.php
      users/create.php
  config.php                 # DB + app config
  bootstrap.php              # autoload, session_start(), error settings
```

---

## Core Concepts + Snippets

1) Front Controller with Routing

```php
// public/index.php
require __DIR__ . '/../bootstrap.php';

use App\Core\{Router, Request, Response};

$router = new Router();

$router->get('/', 'HomeController@index');
$router->get('/users', 'UsersController@index');
$router->get('/users/create', 'UsersController@create');
$router->post('/users', 'UsersController@store');

$request = Request::capture();
$response = $router->dispatch($request);
$response->send();
```

2) Controllers implement orchestration and PRG

```php
// app/Controllers/UsersController.php
namespace App\Controllers;

use App\Core\{Controller, Request, Response, Flash};
use App\Models\User;

class UsersController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::all();
        return $this->view('users/index', ['users' => $users]);
    }

    public function create(Request $request): Response
    {
        return $this->view('users/create');
    }

    public function store(Request $request): Response
    {
        $data = $request->only(['full_name','email','phone']);
        // TODO: validate $data
        User::create($data);

        Flash::set('success', 'User created');
        return $this->redirect('/users'); // PRG: POST -> Redirect -> GET
    }
}
```

3) Views render data (templates + layout)

```php
<!-- app/Views/layout.php -->
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= $title ?? 'App' ?></title>
  <link rel="stylesheet" href="/bulma.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style> body { max-width: 900px; margin: 2rem auto; } </style>
  <?php if (function_exists('section') && section('head')) echo section('head'); ?>
  <?php unset($GLOBALS['__sections']['head']); ?>
  <?php $msg = App\Core\Flash::get('success'); if ($msg): ?>
    <script>console.log('flash: <?= htmlspecialchars($msg, ENT_QUOTES) ?>');</script>
  <?php endif; ?>
</head>
<body>
  <nav><a href="/">Home</a> · <a href="/users">Users</a></nav>
  <main>
    <?= $content ?? '' ?>
  </main>
</body>
</html>
```

```php
<!-- app/Views/users/index.php -->
<?php $title = 'Users'; ?>
<h1 class="title">Users</h1>
<p><a class="button is-link" href="/users/create">New User</a></p>
<ul>
  <?php foreach ($users as $u): ?>
    <li><?= htmlspecialchars($u['full_name']) ?> (<?= htmlspecialchars($u['email']) ?>)</li>
  <?php endforeach; ?>
  <?php if (empty($users)): ?><li>No users yet.</li><?php endif; ?>
</ul>
```

4) Models encapsulate data access

```php
// app/Core/Database.php
namespace App\Core;
use PDO;

class Database {
    public static function pdo(): PDO {
        static $pdo;
        if (!$pdo) {
            $cfg = require __DIR__ . '/../../config.php';
            $pdo = new PDO($cfg['dsn'], $cfg['user'], $cfg['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return $pdo;
    }
}
```

```php
// app/Models/User.php
namespace App\Models;
use App\Core\Database;

class User {
    public static function all(): array {
        return Database::pdo()->query('SELECT * FROM users ORDER BY id DESC')->fetchAll();
    }
    public static function create(array $data): void {
        $stmt = Database::pdo()->prepare('INSERT INTO users (full_name,email,phone) VALUES (?,?,?)');
        $stmt->execute([$data['full_name'],$data['email'],$data['phone']]);
    }
}
```

---

## PRG Inside MVC (Key Talking Points)
- Treat every form POST as a state‑changing action that ends with a redirect.
- After redirect, show a GET page that can be safely refreshed.
- Use a flash message (session) to surface success/errors after redirect.
- Validate input in the controller (or a dedicated validator), not in the view.

Redirect example:

```php
// After successful POST
Flash::set('success', 'Profile updated');
return $this->redirect('/profile');
```

View reads and clears flash:

```php
<?php if ($msg = App\Core\Flash::get('success')): ?>
  <div class="notification is-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
```

---

## Mapping Your Existing Project to MVC
- Each existing page becomes a Controller action (GET for show/index, POST for create/update/delete).
- Shared headers/footers move into a single `layout.php` with small view files per screen.
- Database calls move into Model classes (e.g., `User`, `Post`).
- Form handlers (POST) now: validate -> update DB -> `redirect()` -> show GET page with flash.

Checklist to complete the migration:
- [ ] Create front controller + router
- [ ] Add controllers and move logic out of page scripts
- [ ] Add views and migrate HTML into templates
- [ ] Extract DB code to models (PDO)
- [ ] Implement PRG + flash messages in POST actions

---

## Common Pitfalls
- Mixing DB access inside views — keep it in models.
- Rendering HTML from controllers — return a view instead.
- Forgetting redirects after POST — leads to duplicate submissions.
- Rendering before sending headers — build a Response, then send.

---

## What’s Next
- Lab 1–4 walk you through building the micro MVC from scratch.
- Optional enhancements: CSRF tokens, validation helpers, middleware pattern.
- Later we will compare our micro MVC to a full framework (e.g., Laravel).


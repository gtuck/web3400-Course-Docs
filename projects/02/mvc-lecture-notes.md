# Project 02 Lecture: From PRG to MVC — Build a Micro MVC in PHP

## Learning Objectives
- Explain PRG (Post‑Redirect‑Get) and why it’s useful.
- Describe the MVC pattern and responsibilities of Model, View, Controller.
- Trace the request lifecycle in a front‑controller MVC.
- Build core pieces of a micro MVC: router, controller base, view renderer.
- Implement a simple homepage through Controller → View → Layout.
- Understand how existing PRG scripts map to MVC for future refactors.

---

## Vocabulary
- Concern: A single, well‑defined responsibility (e.g., routing, rendering, data access).
- Bootstrap: Minimal startup code that prepares the app (autoload, session) before handling requests.
- Orchestrate: What controllers do — coordinate request data, business logic, and view rendering.
- Routing: Mapping an HTTP method + path (e.g., GET /) to a controller action.
- Front Controller: One entry point (`public/index.php`) that receives all requests.
- Controller Action: A specific method on a controller that handles a matched route.
- View: Template that renders HTML; receives data but avoids business logic and DB calls.
- Layout: Base HTML wrapper applied around view content (includes `<head>`, global structure).
- Partial: Reusable view snippet (e.g., nav, footer) included inside other views.
- Request: Representation of the incoming HTTP message (method, path, query, form data).
- Response: Status, headers, and body that the server sends back to the client.
- Redirect: A Response that instructs the browser to fetch a different URL (commonly 302).
- PRG: Post‑Redirect‑Get — redirect after handling a POST to prevent resubmission on refresh.
- Autoloading: Loading class files on demand based on their namespace/class name.
- Namespace: Logical grouping of classes (e.g., `App\Core`) to avoid naming conflicts.
- Dispatch: Invoking the controller action associated with the matched route.
- Render: Convert a view + data into HTML string placed inside the layout.
- 200 OK: Success status for normal responses.
- 302 Found: Status for temporary redirects (used in PRG).
- 404 Not Found: Status when no route matches the request.
- Idempotent: Safe to repeat without additional side‑effects (GET should be idempotent).
- State‑Changing: Operations that modify data or server state (POST/PUT/DELETE).
- Cohesion/Coupling: Cohesion is focus of a module’s responsibility; coupling is how much modules depend on each other.
- Flash Message: Short‑lived session message shown once after a redirect (introduced in a later project).

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
2. Index bootstraps autoloading and session
3. Router matches method + path to a controller action (e.g., `HomeController@index`)
4. Controller orchestrates work (optionally call a model in later projects)
5. Controller returns a view (GET) or a redirect response (POST)
6. View templates render HTML using a base layout and sections

ASCII flow:

```
Request -> public/index.php -> Router -> Controller -> Model -> View -> Response
                              (POST success -> Redirect -> GET -> View)
```

---

## Minimal Project Structure (this project)
Keep it framework‑free and focused on a homepage, inside `projects/02/php-micro-mvc`.

```
php-micro-mvc/
  public/
    index.php                # front controller (composer autoload)
  src/
    Router.php               # method+path routing (GET/POST)
    Controller.php           # base controller render() → layout
    Routes/
      index.php              # defines routes and dispatches
    Controllers/
      HomeController.php
    Views/
      layout.php             # head/body/footer (Bulma, FA, BulmaJS)
      index.php              # simple homepage view
  composer.json              # PSR-4 autoload: App\\ → src/
```

---

## Core Concepts + Snippets

1) Front Controller with Routing

```php
// public/index.php
<?php
require '../vendor/autoload.php';
// Defer to the routes file (register + dispatch)
$router = require '../src/Routes/index.php';
```

2) Controller implements orchestration

```php
// src/Controllers/HomeController.php
namespace App\Controllers;
use App\Controller;
class HomeController extends Controller
{
  public function index()
  {
    $this->render('index', ['title' => 'Home - Micro MVC']);
  }
}
```

3) Views render data (templates + layout)

```php
<!-- src/Views/layout.php -->
<?php $pageTitle = $pageTitle ?? ($title ?? 'Site Title'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Project 02 - Micro MVC">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></title>

  <!-- Bulma & Assets (match Projects 00/01) -->
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

<section class="hero is-primary">
  <div class="hero-body">
    <p class="title">Hero title</p>
    <p class="subtitle">Hero subtitle</p>
  </div>
</section>

<section class="section">
  <h1 class="title">Welcome to Micro MVC</h1>
  <h2 class="subtitle">Homepage rendered via Controller → View → Layout</h2>
  </section>
```

---

## PRG Inside MVC (Concept)
- Treat POST handlers as state‑changing actions that end with a redirect.
- Redirect back to a GET page that can be safely refreshed.
- In a future project, add flash messages to show success/errors after redirect.

Redirect example (no flash yet):

```php
// In a POST action
return $this->redirect('/');
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
- [ ] Later: Extract DB code to models (PDO)
- [ ] Later: Implement PRG + flash messages in POST actions

---

## Common Pitfalls
- Mixing DB access inside views — keep it in models.
- Rendering HTML from controllers — return a view instead.
- Forgetting redirects after POST — leads to duplicate submissions.
- Rendering before sending headers — build a Response, then send.

---

## What’s Next
- Labs walk you through building the micro MVC and a homepage using the `php-micro-mvc` folder.
- Optional enhancements: 404 view, simple nav partial.
- Later we will add models, validation, PRG flash, and compare to Laravel.

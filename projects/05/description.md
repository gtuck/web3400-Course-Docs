# Project 05 – Vanilla PHP Template System with Enhanced Security & Validation
Build a lightweight, dependency‑free PHP templating system that supports layout inheritance, named sections, reusable partials, shared data, and a safe escape helper. Enhance your MVC framework with CSRF protection, reusable validation, RESTful routing, and custom exception handling.

---

## Overview
Starting from the end of Project 04 (Dotenv, Database helper, BaseModel + generator, Blog + Contact pages), you will:

1. **Add a dependency‑free `View` class** that renders PHP templates using output buffering
2. **Implement CSRF protection** to secure forms against cross-site request forgery
3. **Create a reusable `Validator` class** for clean, declarative input validation
4. **Extend routing** to support RESTful HTTP methods (PUT, DELETE, PATCH) with method spoofing
5. **Add custom exceptions** for better error handling and debugging

### Templating Features:
- Layout inheritance via `$this->layout('layouts/main')`
- Named sections via `$this->start('content')` and `$this->end()`
- Yield sections in the layout via `$this->section('content')`
- Reusable partials via `$this->insert('partials/nav', [...])`
- Shared data available in all views (`$this->share([...])`)
- Safe escaping helper `$this->e($value)`

### Security & Architecture Features:
- **CSRF tokens** automatically shared with all views
- **Validator class** with rules like `required`, `email`, `max`, `min`, `in`
- **RESTful routing** with PUT/DELETE/PATCH support
- **Custom exceptions** like `RouteNotFoundException`
- **Consistent XSS protection** using `$this->e()` throughout

This is "vanilla PHP": no external templating or validation libraries.

---

## Learning Objectives
- Use output buffering to build a simple templating engine
- Separate page layout/partials from view content
- Integrate a view engine into an MVC base controller
- **Implement CSRF protection** to prevent cross-site request forgery attacks
- **Create reusable validation logic** with a declarative rule-based system
- **Extend routing** to support RESTful HTTP methods beyond GET/POST
- **Use custom exceptions** for better error handling and debugging
- **Apply consistent XSS protection** throughout all views
- Follow security best practices (timing-safe comparisons, prepared statements, mass assignment protection)

---

## Prerequisites
- Start from your completed Project 04 (recommended): Dotenv bootstrapped in `public/index.php`, `Support/Database.php`, `Models/BaseModel.php`, generated models (e.g., `Blog`, `Contact`), and `/contact` GET/POST routes.
- Composer PSR‑4 autoload in place (`App\ => src/`).
- Your router + controllers working from Project 04.

---

## Target Structure

```
projects/05/
  composer.json              # PSR‑4 autoload (App\ => src/), includes phpdotenv
  public/
    index.php                # Front controller (session start, Dotenv from P04)
  src/
    Controller.php           # Base controller with View, CSRF, flash, redirect
    Router.php               # Enhanced with PUT/DELETE/PATCH + method spoofing
    Support/
      Database.php           # From P04
      View.php               # Vanilla template engine
      Validator.php          # NEW – Reusable validation class
    Exceptions/
      RouteNotFoundException.php  # NEW – Custom 404 exception
    Models/
      BaseModel.php          # From P04 (Active Record pattern)
      Blog.php               # Clean array syntax, documented
      Contact.php            # Clean array syntax, documented
    Controllers/
      HomeController.php     # Renders with View, documented
      ContactController.php  # Uses Validator + CSRF, documented
    Routes/
      index.php              # Routes: /, /contact (GET + POST)
    Views/
      layouts/
        main.php             # Site layout (yields sections)
      partials/
        head.php             # <head> + CSS/JS
        nav.php              # Top navigation
        flash.php            # Flash/status messages
        footer.php           # Footer
      index.php              # Homepage view (consistent XSS protection)
      contact.php            # Contact form (includes CSRF token)
  scripts/
    generate-model.php       # Enhanced to output clean array syntax
```

---

## Step 1) Copy your previous project into `projects/05`

From the repository root:

```bash
cp -r projects/04 projects/05
```

If you’re coming from P03, you must also add the P04 pieces (Dotenv bootstrapping, `Support/Database.php`, `Models/BaseModel.php`, generated models, contact controller + routes) before continuing. The goal is to begin where Project 04 concluded.

---

## Step 2) Create files/folders for the templating system

From `projects/05/`:

```bash
mkdir -p src/Support src/Views/{layouts,partials} && \
touch src/Support/View.php \
src/Views/layouts/main.php \
src/Views/partials/{head.php,nav.php,flash.php,footer.php} \
src/Views/index.php
```

---

## Step 3) Implement the vanilla `View` engine

Create `src/Support/View.php` with a tiny, dependency‑free template system using output buffering. It exposes layout/section/partial helpers to templates via `$this`.

```php
<?php
// filepath: projects/05/src/Support/View.php
namespace App\Support;

final class View
{
    private string $basePath;
    private string $ext;
    private ?string $layout = null;
    private array $sections = [];
    private array $sectionStack = [];
    private array $shared = [];

    public function __construct(string $basePath, string $ext = 'php', array $shared = [])
    {
        $this->basePath = rtrim($basePath, '/');
        $this->ext = ltrim($ext, '.');
        $this->shared = $shared;
    }

    public function share(array $vars): void
    {
        $this->shared = $vars + $this->shared;
    }

    public function layout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function start(string $section): void
    {
        $this->sectionStack[] = $section;
        ob_start();
    }

    public function end(): void
    {
        $section = array_pop($this->sectionStack);
        if ($section === null) {
            throw new \RuntimeException('No active section to end().');
        }
        $this->sections[$section] = ($this->sections[$section] ?? '') . ob_get_clean();
    }

    public function section(string $name, string $default = ''): void
    {
        echo $this->sections[$name] ?? $default;
    }

    public function insert(string $view, array $data = []): void
    {
        $this->includeFile($this->resolve($view), $data);
    }

    public function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function render(string $view, array $data = []): string
    {
        // Reset per-render state
        $this->layout = null;
        $this->sections = [];
        $this->sectionStack = [];

        // Render the view first
        ob_start();
        $this->includeFile($this->resolve($view), $data);
        $viewOutput = ob_get_clean();

        // If a layout was declared, make the full view output the default `content`
        if ($this->layout) {
            if (!isset($this->sections['content'])) {
                $this->sections['content'] = $viewOutput;
            }

            ob_start();
            $this->includeFile($this->resolve($this->layout), $data);
            return ob_get_clean();
        }

        return $viewOutput;
    }

    private function resolve(string $view): string
    {
        // Support dotted or slashed notation: e.g., 'layouts.main' or 'layouts/main'
        $clean = str_replace(['\\\\', '..'], ['/', ''], $view);
        $clean = str_replace('.', '/', $clean);
        $path = $this->basePath . '/' . $clean . '.' . $this->ext;
        if (!is_file($path)) {
            throw new \RuntimeException("View not found: {$path}");
        }
        return $path;
    }

    private function includeFile(string $path, array $vars): void
    {
        // Shared first, then per-render data (per-render wins on key conflicts)
        extract($this->shared, EXTR_SKIP);
        extract($vars, EXTR_SKIP);
        include $path; // `$this` is available inside included templates
    }
}
```

Notes:
- Templates are plain PHP files living under `src/Views/`.
- Inside templates, `$this` refers to the `View` instance, so you can call `$this->layout()`, `$this->start()/end()`, `$this->section()`, `$this->insert()`, and `$this->e()` directly.
- The entire view output becomes the `content` section automatically if a layout is set and `content` wasn’t manually defined.

---

## Step 4) Update the base `Controller` to use `View` and add CSRF protection

Modify `src/Controller.php` to render through the new engine and add CSRF protection helpers.

```php
<?php
// filepath: projects/05/src/Controller.php
namespace App;

use App\Support\View;

class Controller
{
    protected View $view;

    public function __construct()
    {
        // Point to the Views directory relative to this file
        $this->view = new View(__DIR__ . '/Views');

        // Share site‑wide variables
        $siteName = $_ENV['SITE_NAME'] ?? 'My PHP Site';
        $siteEmail = $_ENV['SITE_EMAIL'] ?? 'email@website.com';
        $sitePhone = $_ENV['SITE_PHONE'] ?? '123-321-9876';

        $this->view->share([
            'siteName' => $siteName,
            'siteEmail' => $siteEmail,
            'sitePhone' => $sitePhone,
            'csrfToken' => $this->csrfToken(),  // NEW: Share CSRF token
        ]);
    }

    protected function render(string $view, array $data = []): void
    {
        echo $this->view->render($view, $data);
    }

    // Flash a message for next request
    protected function flash(string $text, string $type = 'is-info'): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['messages'] = $_SESSION['messages'] ?? [];
        $_SESSION['messages'][] = compact('type', 'text');
    }

    // Redirect with default 303 (PRG)
    protected function redirect(string $path, int $status = 303): void
    {
        header('Location: ' . $path, true, $status);
        exit;
    }

    // NEW: Generate or retrieve CSRF token
    protected function csrfToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // NEW: Validate CSRF token (timing-safe comparison)
    protected function validateCsrf(string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}
```

---

## Step 4a) Add site info to your environment

Add app/site details to `.env` and to `.env.example` so others can run your project.

Example `.env` additions:

```
SITE_NAME=My PHP Site
SITE_EMAIL=email@website.com
SITE_PHONE=123-321-9876
```

Example `.env.example` additions:

```
SITE_NAME=My PHP Site
SITE_EMAIL=email@website.com
SITE_PHONE=123-321-9876
```

Tip: You can enforce them with Dotenv if you prefer strictness:

```php
// in public/index.php after safeLoad():
$dotenv->required(['DB_HOST','DB_NAME','DB_USER','DB_PASS','DB_CHARSET','SITE_NAME','SITE_EMAIL','SITE_PHONE'])->notEmpty();
```

---

## Step 5) Create a layout and partials

Layout `src/Views/layouts/main.php` defines the base HTML shell and yields sections:

```php
<?php // filepath: projects/05/src/Views/layouts/main.php ?>
<!doctype html>
<html lang="en">
  <head>
    <?php $this->insert('partials/head', ['title' => ($title ?? 'Home') . ' – ' . ($siteName ?? 'Site')]); ?>
  </head>
  <body class="has-navbar-fixed-top">
    <?php $this->insert('partials/nav'); ?>
    <?php $this->insert('partials/flash'); ?>

    <main class="container">
      <?php $this->section('content'); ?>
    </main>

    <?php $this->insert('partials/footer'); ?>
  </body>
  </html>
```

Example partials (keep them minimal for the project):

`src/Views/partials/head.php`
```php
<?php // Basic head partial; you can add Bulma/FA/CDN links as needed ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?= $this->e($title ?? ($siteName ?? 'Site')) ?></title>
  
  <link rel="preconnect" href="https://cdn.jsdelivr.net">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
  <script src="https://cdn.jsdelivr.net/npm/@vizuaalog/bulmajs@0.12/dist/bulma.min.js" defer></script>

```

`src/Views/partials/nav.php`
```php
<header class="container">
    <nav class="navbar is-fixed-top is-spaced has-shadow" role="navigation" aria-label="main navigation">
      <div class="navbar-brand">
        <a class="navbar-item" href="/">
          <span class="icon-text">
            <span class="icon"><i class="fas fa-code" aria-hidden="true"></i></span>
            <span><?= $this->e($title ?? ($siteName ?? 'Site')) ?></span>
          </span>
        </a>

        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarMenu">
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
        </a>
      </div>

      <div id="navbarMenu" class="navbar-menu">
        <div class="navbar-start">
        </div>
        <div class="navbar-end">
            <a class="navbar-item" href="/contact">Contact</a>
        </div>
      </div>
    </nav>
  </header>
```

`src/Views/partials/flash.php`
```php
<?php
if (!empty($_SESSION['messages'])):
?>
    <section class="section">
        <?php foreach ($_SESSION['messages'] as $m): ?>
            <div class="notification <?= htmlspecialchars($m['type'], ENT_QUOTES) ?>">
                <button class="delete" data-bulma="notification"></button>
                <?= htmlspecialchars($m['text'], ENT_QUOTES) ?>
            </div>
        <?php endforeach;
        $_SESSION['messages'] = []; ?>
    </section>
<?php endif; ?>
```

`src/Views/partials/footer.php`
```php
 <!-- BEGIN PAGE FOOTER -->
  <footer class="footer">
    <div class="content has-text-centered">
      <p>Your footer goes here.</p>
    </div>
  </footer>
  <!-- END PAGE FOOTER -->
```

---

## Step 5a) Start the PHP session (for flash messages)

Flash notifications use `$_SESSION`. Enable sessions in your front controller:

```php
<?php
// filepath: projects/05/public/index.php
require '../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load env and route as usual...
```

---

## Step 6) Create a view that uses the layout and sections

`src/Views/index.php`
```php
<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>

  <section class="hero is-primary">
    <div class="hero-body">
      <h1 class="title">Welcome to <?= $this->e($siteName ?? 'My PHP Site') ?></h1>
      <p class="subtitle">This page is rendered with a vanilla PHP template engine.</p>
    </div>
  </section>

  <h2>Blog Posts</h2>
  <?php foreach ($posts as $post): ?>
    <article class="box">
      <h3><?= $this->e($post['title']) ?></h3>
      <p><?= $this->e($post['body']) ?></p>
    </article>
  <?php endforeach; ?>

<?php $this->end(); ?>
```

---

Note: If you prefer `home.php`, you can render that instead from the controller.

---

## Step 7) Render from a controller (Home)

Example `HomeController` (note: use static method call, not instantiation):

```php
<?php
// filepath: projects/05/src/Controllers/HomeController.php
namespace App\Controllers;

use App\Controller;
use App\Models\Blog;

class HomeController extends Controller
{
    public function index(): void
    {
        // Fetch blog posts using static method (BaseModel pattern)
        $posts = Blog::all(orderBy: 'created_at DESC');

        $this->render('index', [
            'title' => 'Home',
            'posts' => $posts,
        ]);
    }
}
```

**Important:** Since `Blog` extends `BaseModel`, all CRUD methods are static. Use `Blog::all()`, not `$blog->all()`.

Your router should dispatch `/` to `HomeController@index` as in P03/P04.

---

## Step 8) Keep the `contact` routes from Project 04

Ensure `src/Routes/index.php` still contains the GET and POST contact routes from P04:

```php
<?php
use App\Controllers\HomeController;
use App\Controllers\ContactController;
use App\Router;

$router = new Router();

$router->get('/', HomeController::class, 'index');
$router->get('/contact', ContactController::class, 'show');
$router->post('/contact', ContactController::class, 'submit');

$router->dispatch();
```

---

## Step 9) Update `ContactController` with CSRF protection and Validator

Enhance `ContactController` to use CSRF validation and the new `Validator` class.

```php
<?php
// filepath: projects/05/src/Controllers/ContactController.php
namespace App\Controllers;

use App\Controller;
use App\Models\Contact;
use App\Support\Validator;

class ContactController extends Controller
{
    public function show()
    {
        $this->render('contact', [
            'title' => 'Contact Us',
            'old'   => ['name' => '', 'email' => '', 'message' => ''],
        ]);
    }

    public function submit()
    {
        // CSRF Protection: Validate token first
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed. Please try again.', 'is-danger');
            $this->redirect('/contact');
        }

        // Sanitize input
        $data = [
            'name'    => trim($_POST['name'] ?? ''),
            'email'   => trim($_POST['email'] ?? ''),
            'message' => trim($_POST['message'] ?? ''),
        ];

        // Validate using Validator class
        $validationErrors = Validator::validate($data, [
            'name'    => 'required|max:255',
            'email'   => 'required|email',
            'message' => 'required',
        ]);

        // Flatten errors to simple array
        $errors = Validator::flattenErrors($validationErrors);

        if (!empty($errors)) {
            foreach ($errors as $err) {
                $this->flash($err, 'is-warning');
            }
            return $this->render('contact', [
                'title' => 'Contact Us',
                'old'   => $data,
            ]);
        }

        // Persist via BaseModel-powered Contact model
        Contact::create($data);

        // Flash success and redirect (POST/Redirect/GET)
        $this->flash('Thanks! Your message has been received.', 'is-success');
        $this->redirect('/contact');
    }
}
```

### Step 9a) Create the Validator class

Create `src/Support/Validator.php` with reusable validation logic:

```php
<?php
// filepath: projects/05/src/Support/Validator.php
namespace App\Support;

class Validator
{
    public static function validate(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule, 2);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;

                $error = self::applyRule($field, $value, $ruleName, $ruleParam);
                if ($error) {
                    $errors[$field] = $errors[$field] ?? [];
                    $errors[$field][] = $error;
                }
            }
        }
        return $errors;
    }

    private static function applyRule(string $field, mixed $value, string $rule, ?string $param): ?string
    {
        return match ($rule) {
            'required' => ($value === null || $value === '') ? ucfirst($field) . " is required." : null,
            'email' => ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) ? ucfirst($field) . " must be a valid email." : null,
            'max' => ($value && mb_strlen($value) > (int)$param) ? ucfirst($field) . " must not exceed {$param} characters." : null,
            default => null,
        };
    }

    public static function flattenErrors(array $errors): array
    {
        $flat = [];
        foreach ($errors as $fieldErrors) {
            foreach ($fieldErrors as $message) {
                $flat[] = $message;
            }
        }
        return $flat;
    }
}
```

---

## Step 10) Convert the `contact` view to use the layout and CSRF token

Create or replace `src/Views/contact.php` with CSRF protection and consistent XSS escaping:

```php
<?php $this->layout('layouts/main'); ?>

<?php $this->start('content'); ?>

<h1>Contact Us</h1>

<form method="post" action="/contact" novalidate>
    <!-- CSRF Protection Token -->
    <input type="hidden" name="csrf_token" value="<?= $this->e($csrfToken) ?>">

    <div class="field">
        <label class="label" for="name">Name</label>
        <div class="control">
            <input id="name" name="name" class="input" type="text" required
                value="<?= $this->e($old['name'] ?? '') ?>" placeholder="Your name">
        </div>
    </div>

    <div class="field">
        <label class="label" for="email">Email</label>
        <div class="control">
            <input id="email" name="email" class="input" type="email" required
                value="<?= $this->e($old['email'] ?? '') ?>" placeholder="you@example.com">
        </div>
    </div>

    <div class="field">
        <label class="label" for="message">Message</label>
        <div class="control">
            <textarea id="message" name="message" class="textarea" required
                placeholder="How can we help?"><?= $this->e($old['message'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="field is-grouped">
        <div class="control">
            <button type="submit" class="button is-primary">
                <span class="icon"><i class="fas fa-paper-plane" aria-hidden="true"></i></span>
                <span>Send</span>
            </button>
        </div>
        <div class="control">
            <a class="button is-light" href="/">Cancel</a>
        </div>
    </div>
</form>

<?php $this->end(); ?>
```

**Key Changes:**
- Added CSRF token as hidden field: `<input type="hidden" name="csrf_token" value="<?= $this->e($csrfToken) ?>">`
- Use `$this->e()` for all dynamic values (consistent XSS protection)

---

## Run and Test

From `projects/05/` (or the repo root, adjust paths accordingly):

```bash
composer dump-autoload
php -S 0.0.0.0:8000 -t public
```

Open `http://localhost:8000/` and verify:
- The page renders using the layout
- The home page content appears in the layout’s `content` section
- Partials render without duplication
- Escaping works: `<?= $this->e('<b>XSS</b>') ?>` prints safely

Also verify `http://localhost:8000/contact`:
- GET renders the contact form within the layout
- Invalid POSTs show validation errors and repopulate inputs
- Valid POSTs persist to DB via `Contact::create()`, set a success flash, and redirect to GET

---

## Tips and Gotchas

### Templating
- Always escape untrusted data with `$this->e()` inside templates (use consistently, not raw `htmlspecialchars()`)
- Keep templates dumb: no DB access or heavy logic
- Use `$this->insert('partials/...')` for reusable chunks
- If you forget `end()`, you'll get a "No active section to end()" error
- If a layout is set and no `content` section was defined, the entire view output becomes `content` automatically
- Share globals like `siteName`, `csrfToken`, etc. via `$this->view->share([...])` in the base controller constructor

### Security
- **CSRF tokens** prevent cross-site request forgery—validate on all POST/PUT/DELETE/PATCH requests
- Use **timing-safe comparison** (`hash_equals()`) when validating tokens to prevent timing attacks
- Always validate CSRF **before** processing form data
- The CSRF token is automatically shared with all views, just add it to forms as a hidden field

### Models & Data
- BaseModel methods are **static**: use `Blog::all()`, not `(new Blog())->all()`
- Models use `$fillable` for **mass assignment protection**—only listed fields can be set via `create()` or `update()`
- Use clean array syntax: `['field1', 'field2']` not `array(0 => 'field1', 1 => 'field2')`

### Validation
- Use the `Validator` class instead of manual validation for cleaner, reusable code
- Validation rules are declarative: `'email' => 'required|email|max:255'`
- Use `Validator::flattenErrors()` to convert nested errors to a simple array for flash messages

### Routing
- Router now supports RESTful methods: `$router->put()`, `$router->delete()`, `$router->patch()`
- HTML forms only support GET/POST, use method spoofing: `<input type="hidden" name="_method" value="DELETE">`
- Use custom exceptions like `RouteNotFoundException` for better debugging

### Best Practices
- Prefer the **PRG pattern** (POST-Redirect-GET): after successful POSTs, set a flash and redirect to avoid duplicate submissions
- Keep controllers thin: validation goes in Validator, data operations in Models
- Document your code with PHPDoc comments explaining purpose and usage examples

---

## Grading Checklist

### Core Templating (Original Requirements)
- [ ] Project exists at `projects/05/` and runs
- [ ] `src/Support/View.php` implements layout/sections/partials and `e()`
- [ ] `src/Controller.php` renders through the `View` engine
- [ ] Layout at `src/Views/layouts/main.php` yields at least a `content` section
- [ ] Partials present (`partials/head.php`, `partials/nav.php`, `partials/footer.php`, `partials/flash.php`)
- [ ] Homepage view (`index.php`) uses layout and defines a `content` section
- [ ] Contact view (`contact.php`) uses layout and preserves old input
- [ ] Routes include `/` and `/contact` GET/POST
- [ ] No external templating libraries used
- [ ] PHP session started in `public/index.php`
- [ ] `ContactController` uses flash notifications and PRG on success

### Enhanced Security & Features
- [ ] **CSRF Protection:** Controller has `csrfToken()` and `validateCsrf()` methods
- [ ] **CSRF Token:** Automatically shared with all views in Controller constructor
- [ ] **CSRF Validation:** `ContactController::submit()` validates token before processing
- [ ] **CSRF in Form:** Contact form includes hidden CSRF token field
- [ ] **Validator Class:** `src/Support/Validator.php` implements rule-based validation
- [ ] **Validator Usage:** `ContactController` uses `Validator::validate()` instead of manual validation
- [ ] **Consistent XSS:** All views use `$this->e()` for dynamic values (not raw `htmlspecialchars()`)
- [ ] **Custom Exceptions:** `src/Exceptions/RouteNotFoundException.php` exists
- [ ] **Router Updates:** Router throws `RouteNotFoundException` instead of generic `Exception`
- [ ] **RESTful Routing:** Router supports `put()`, `delete()`, `patch()` methods
- [ ] **Method Spoofing:** Router's `dispatch()` checks for `_method` field
- [ ] **Clean Models:** Blog and Contact models use clean array syntax: `['field1', 'field2']`
- [ ] **Static Model Calls:** `HomeController` uses `Blog::all()`, not `$blog->all()`
- [ ] **Model Generator:** `scripts/generate-model.php` outputs clean array syntax
- [ ] **Documentation:** Key files have PHPDoc comments explaining purpose and usage

---

## Submission
Submit the direct URL to your Project 05 folder in your repository (replace YOUR‑USER and repo name):

```
https://github.com/YOUR-USER/YOUR-REPO/blob/main/projects/05/
```

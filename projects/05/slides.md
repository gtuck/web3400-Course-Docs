---
theme: default
title: Vanilla PHP Template System + Security & Validation
info: |
  Build a tiny templating engine with CSRF protection, reusable validation, RESTful routing, and custom exceptions
layout: cover
highlighter: shiki
lineNumbers: true
drawings:
  persist: false
transition: slide-left
mdc: true
download: true
exportFilename: vanilla-php-templates-security-validation
class: text-center
---

# Vanilla PHP Template System

## + Security & Validation

Layouts • Sections • Partials • Escaping • Flash • PRG

**CSRF • Validator • REST • Exceptions**

---
layout: two-cols
---

# Recap (Project 04)

You built:
- Dotenv config + `.env/.env.example`
- `Database::pdo()` (centralized PDO)
- `BaseModel` + generator
- Contact form (GET/POST)

::right::

# Today (Project 05)

**Templating:**
- Tiny `View` class (output buffering)
- Layouts, named sections, partials
- Shared data + escape helper
- Flash notifications (session)
- PRG (303 redirect after POST)

**Security & Architecture:**
- CSRF protection (tokens + validation)
- Reusable `Validator` class
- RESTful routing (PUT/DELETE/PATCH)
- Custom exceptions

---

# Why a Template System?

Problems it solves:
- Reuse shared layout chrome (head/nav/footer)
- Keep views simple and structured
- Encapsulate escaping/partials helpers

Constraints:
- Vanilla PHP (no external template libs)
- Minimal API; easy to reason about

---

# `View` API (at a glance)

Helpers available inside templates via `$this`:
- `layout('layouts/main')`
- `start('content')` ... `end()`
- `section('content')`
- `insert('partials/nav', ['key'=> 'val'])`
- `share([...])` (controller side)
- `e($value)` (escape) — **use consistently!**

Automatically shared:
- `$siteName`, `$siteEmail`, `$sitePhone`
- `$csrfToken` (for forms)

---

# Engine (Core Idea)

Output buffering + include files:
```php
ob_start();
extract($vars, EXTR_SKIP);
include $path;
$content = ob_get_clean();
```

- Store named sections during `start()/end()`
- Render view first; then layout if declared

---

# Layout + Sections (Example)

Layout (`layouts/main.php`):
```php
<html>
  <head><?php $this->insert('partials/head', ['title' => $title ?? 'Home']) ?></head>
  <body>
    <?php $this->insert('partials/nav') ?>
    <?php $this->insert('partials/flash') ?>
    <main><?php $this->section('content') ?></main>
    <?php $this->insert('partials/footer') ?>
  </body>
</html>
```

View (`index.php`):
```php
<?php $this->layout('layouts/main') ?>
<?php $this->start('content') ?>
  <h1><?= $this->e($siteName) ?></h1>
<?php $this->end() ?>
```

---

# Partials + Shared Data

Shared in base controller:
```php
$this->view->share(['siteName' => $_ENV['SITE_NAME'] ?? 'My PHP Site']);
```

Partial (`partials/nav.php`):
```php
<a href="/"><?= $this->e($siteName ?? 'Site') ?></a>
```

---

# Escape Helper

Use `$this->e()` for untrusted values:
```php
<h2><?= $this->e($post['title']) ?></h2>
<p><?= $this->e($post['body']) ?></p>
```

Avoid echoing raw request/DB values in templates.

---

# Controller Integration

Base controller:
- Creates `View` with `__DIR__ . '/Views'`
- Shares `SITE_NAME`, `SITE_EMAIL`, `SITE_PHONE` from env
- **Shares `csrfToken()` automatically**
- Adds `flash($text, $type)` helper
- Adds `redirect($path, $status=303)` helper
- **Adds `csrfToken()` and `validateCsrf()` for security**

---

# Flash Notifications

Session-backed messages:
```php
$this->flash('Saved!', 'is-success');
```

Partial renders + clears:
```php
<?php if (!empty($_SESSION['messages'])): ?>
  <?php foreach ($_SESSION['messages'] as $m): ?>
    <div class="notification <?= $this->e($m['type']) ?>"><?= $this->e($m['text']) ?></div>
  <?php endforeach; $_SESSION['messages'] = []; ?>
<?php endif; ?>
```

Remember to `session_start()` in `public/index.php`.

---

# PRG (POST/Redirect/GET)

Flow:
1) POST /contact → validate + persist
2) Flash success
3) Redirect (303) to GET /contact
4) GET renders with flash (no re-submit on refresh)

Benefits:
- Prevents duplicate submissions
- Clear navigation behavior

---

# CSRF Protection

**What is CSRF?**
Cross-Site Request Forgery: malicious sites trick users into submitting forms on your site.

**Solution:**
- Generate unique token per session
- Include in forms as hidden field
- Validate on POST before processing

```php
// Controller
protected function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

protected function validateCsrf(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token); // Timing-safe!
}
```

---

# CSRF in Forms

Include token in every form:
```php
<form method="post" action="/contact">
    <!-- CSRF Protection -->
    <input type="hidden" name="csrf_token" value="<?= $this->e($csrfToken) ?>">

    <!-- Rest of form fields -->
</form>
```

Validate in controller:
```php
public function submit() {
    // First, validate CSRF
    if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
        $this->flash('Security token validation failed', 'is-danger');
        $this->redirect('/contact');
    }

    // Now safe to process form
}
```

---

# Reusable Validator Class

**Problem:** Manual validation is repetitive and error-prone

**Solution:** Declarative rules-based validation

```php
// Support/Validator.php
$errors = Validator::validate($_POST, [
    'name'    => 'required|max:255',
    'email'   => 'required|email',
    'message' => 'required',
    'age'     => 'numeric|min:18|max:120',
    'role'    => 'in:admin,user,guest'
]);

if (!empty($errors)) {
    $messages = Validator::flattenErrors($errors);
    foreach ($messages as $msg) {
        $this->flash($msg, 'is-warning');
    }
}
```

---

# Validator Implementation

```php
class Validator {
    public static function validate(array $data, array $rules): array {
        $errors = [];
        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            foreach ($fieldRules as $rule) {
                [$ruleName, $param] = explode(':', $rule, 2) + [null, null];
                $error = self::applyRule($field, $data[$field] ?? null, $ruleName, $param);
                if ($error) $errors[$field][] = $error;
            }
        }
        return $errors;
    }

    private static function applyRule($field, $value, $rule, $param): ?string {
        return match ($rule) {
            'required' => ($value === null || $value === '') ? ucfirst($field) . " is required." : null,
            'email' => ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) ? "Invalid email." : null,
            'max' => ($value && mb_strlen($value) > (int)$param) ? "Max {$param} chars." : null,
            default => null,
        };
    }
}
```

---

# RESTful Routing

HTML forms only support GET/POST, but REST needs PUT/DELETE/PATCH.

**Solution: Method Spoofing**

Router enhancement:
```php
public function dispatch() {
    $method = $_SERVER['REQUEST_METHOD'];

    // Check for method override
    if ($method === 'POST' && isset($_POST['_method'])) {
        $method = strtoupper($_POST['_method']);
    }

    // Now can match PUT, DELETE, PATCH routes
}
```

Usage in forms:
```html
<form method="POST" action="/users/42">
    <input type="hidden" name="_method" value="DELETE">
    <button>Delete User</button>
</form>
```

---

# RESTful Router Methods

```php
// Router.php
class Router {
    public function get($route, $controller, $action) { }
    public function post($route, $controller, $action) { }
    public function put($route, $controller, $action) { }      // NEW
    public function delete($route, $controller, $action) { }   // NEW
    public function patch($route, $controller, $action) { }    // NEW
}

// Routes/index.php
$router->get('/users', UserController::class, 'index');
$router->post('/users', UserController::class, 'create');
$router->get('/users/{id}', UserController::class, 'show');
$router->put('/users/{id}', UserController::class, 'update');
$router->delete('/users/{id}', UserController::class, 'destroy');
```

---

# Custom Exceptions

**Better than generic `Exception`:**

```php
// Exceptions/RouteNotFoundException.php
class RouteNotFoundException extends \Exception {
    public function __construct(string $method, string $uri) {
        parent::__construct("No route found for {$method} {$uri}", 404);
    }
}

// Router.php
if (!isset($this->routes[$method][$uri])) {
    throw new RouteNotFoundException($method, $uri);
}
```

Benefits:
- Type-safe error handling
- HTTP status codes (404)
- Better debugging
- Can be caught specifically

---

# Clean Model Syntax

**Before (var_export):**
```php
protected static array $fillable = array (
  0 => 'title',
  1 => 'slug',
  2 => 'body',
);
```

**After (clean array literal):**
```php
protected static array $fillable = ['title', 'slug', 'body'];
```

**Update generator:**
```php
function format_array(array $items): string {
    $quoted = array_map(fn($item) => "'{$item}'", $items);
    return '[' . implode(', ', $quoted) . ']';
}
```

---

# Static Model Methods

**BaseModel uses static methods:**

```php
// ❌ Wrong (creates unnecessary instance)
$blog = new Blog();
$posts = $blog->all();

// ✅ Correct (static call)
$posts = Blog::all(orderBy: 'created_at DESC');

// All CRUD methods are static:
Blog::find(1);
Blog::create($data);
Blog::update(1, $data);
Blog::delete(1);
```

---

# Consistent XSS Protection

**Use `$this->e()` everywhere, not raw `htmlspecialchars()`:**

```php
// ❌ Inconsistent
<h2><?= htmlspecialchars($post['title']) ?></h2>
<p><?= $this->e($post['body']) ?></p>

// ✅ Consistent
<h2><?= $this->e($post['title']) ?></h2>
<p><?= $this->e($post['body']) ?></p>
<input value="<?= $this->e($old['name'] ?? '') ?>">
```

**Why?**
- Single method to remember
- Part of View API
- Easier to audit for security

---

# Demo Plan (Enhanced)

1) Add `View` class
2) Wire base controller + share env vars + **CSRF token**
3) Build `layouts/main.php` + partials
4) Convert home + contact views (**use `$this->e()` consistently**)
5) Start session; add flash partial
6) Add **`Validator` class** with rules
7) Update controller to use `flash()` + `redirect()` + **`validateCsrf()`**
8) Add **CSRF token to forms**
9) Replace manual validation with **`Validator::validate()`**
10) Add **REST methods** to Router
11) Create **custom exceptions**
12) Test GET/POST + PRG + **CSRF**

---

# Common Pitfalls

**Templating:**
- Forgetting to start the session for flash
- Missing `end()` after `start()`
- Not escaping user/DB data
- Rendering without setting layout/section

**Security:**
- Forgetting CSRF token in forms
- Not validating CSRF before processing
- Using generic Exception instead of custom
- Mixing escaping methods (htmlspecialchars vs e())

**Models:**
- Instantiating models instead of static calls
- var_export array format instead of clean syntax

---

# Security Checklist

✅ CSRF token in all forms
✅ CSRF validation before form processing
✅ Timing-safe comparison (`hash_equals()`)
✅ Consistent XSS protection (`$this->e()`)
✅ Validator for input validation
✅ Prepared statements (via BaseModel)
✅ Mass assignment protection (`$fillable`)
✅ PRG pattern (prevent duplicate submissions)
✅ Session security (started early)
✅ Custom exceptions with HTTP codes

---

# References

**Core:**
- `src/Support/View.php`
- `src/Support/Validator.php`
- `src/Controller.php` (CSRF methods)
- `src/Router.php` (REST + exceptions)

**Views:**
- `src/Views/layouts/main.php`
- `src/Views/partials/*`
- `src/Views/contact.php` (CSRF token)

**Controllers:**
- `src/Controllers/ContactController.php` (Validator + CSRF)

**Exceptions:**
- `src/Exceptions/RouteNotFoundException.php`

# Project 05 – Vanilla PHP Template System (Layouts, Sections, Partials)
Build a lightweight, dependency‑free PHP templating system that supports layout inheritance, named sections, reusable partials, shared data, and a safe escape helper. Integrate it with your existing MVC structure (from P03/P04) by updating the base controller to render views through this template engine.

---

## Overview
You will create a small `View` class that renders PHP templates using output buffering. Views can declare a layout, define sections with `start()/end()`, yield those sections in the layout with `section()`, and include partials with `insert()`.

Key capabilities:
- Layout inheritance via `$this->layout('layouts/main')`
- Named sections via `$this->start('content')` and `$this->end()`
- Yield sections in the layout via `$this->section('content')`
- Reusable partials via `$this->insert('partials/nav', [...])`
- Shared data available in all views (`$this->share([...])`)
- Safe escaping helper `$this->e($value)`

This is “vanilla PHP”: no external templating libraries.

---

## Learning Objectives
- Use output buffering to build a simple templating engine
- Separate page layout/partials from view content
- Avoid global state; keep rendering concerns in a small class
- Integrate a view engine into an MVC base controller
- Safely escape output to prevent XSS

---

## Prerequisites
- Project 03 or 04 structure in place (`public/`, `src/`, `composer.json`, PSR‑4 autoload)
- Your router + controllers working from earlier projects

---

## Target Structure

```
projects/05/
  composer.json              # PSR‑4 autoload (App\ => src/)
  public/
    index.php                # Front controller
  src/
    Controller.php           # Base controller updated to use View
    Router.php               # From P03/P04
    Support/
      View.php               # NEW – vanilla template engine
    Controllers/
      HomeController.php     # Example usage
    Views/
      layouts/
        main.php             # Site layout (yields sections)
      partials/
        head.php             # <head> + CSS/JS
        nav.php              # Top navigation
        flash.php            # Flash/status messages (optional)
        footer.php           # Footer
      home.php               # Example page view (defines sections)
```

---

## Step 1) Copy your previous project into `projects/05`

From the repository root:

```bash
cp -r projects/04 projects/05
```

If you’re coming from P03, copying that is fine too. The key is that you have a working `public/index.php`, router, controllers, and PSR‑4 autoload.

---

## Step 2) Create files/folders for the templating system

From `projects/05/`:

```bash
mkdir -p src/Support src/Views/{layouts,partials} && \
touch src/Support/View.php \
src/Views/layouts/main.php \
src/Views/partials/{head.php,nav.php,flash.php,footer.php} \
src/Views/home.php
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

## Step 4) Update the base `Controller` to use `View`

Modify `src/Controller.php` so controllers render through the new engine. Example implementation:

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
        // Optionally share site‑wide variables
        $this->view->share([
            'siteName' => 'My PHP Site',
        ]);
    }

    protected function render(string $view, array $data = []): void
    {
        echo $this->view->render($view, $data);
    }
}
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

## Step 6) Create a view that uses the layout and sections

`src/Views/home.php`
```php
<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>

  <h1>Welcome to <?= $this->e($siteName ?? 'My PHP Site') ?></h1>
  <p>This page is rendered with a vanilla PHP template engine.</p>

<?php $this->end(); ?>
```

---

## Step 7) Render from a controller

Example `HomeController`:

```php
<?php
// filepath: projects/05/src/Controllers/HomeController.php
namespace App\Controllers;

use App\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('home', [
            'title' => 'Home',
        ]);
    }
}
```

Your router should dispatch `/` to `HomeController@index` as in P03/P04. No router changes are strictly required for this project beyond ensuring the home route points to the controller above.

---

## Run and Test

From `projects/05/` (or the repo root, adjust paths accordingly):

```bash
composer dump-autoload
php -S 0.0.0.0:8000 -t public
```

Open `http://localhost:8000/` and verify:
- The page renders using the layout
- The `home.php` content appears in the layout’s `content` section
- Partials render without duplication
- Escaping works: `<?= $this->e('<b>XSS</b>') ?>` prints safely

---

## Tips and Gotchas
- Always escape untrusted data with `$this->e()` inside templates.
- Keep templates dumb: no DB access or heavy logic.
- Use `$this->insert('partials/...')` for reusable chunks.
- If you forget `end()`, you’ll get a “No active section to end()” error.
- If a layout is set and no `content` section was defined, the entire view output becomes `content` automatically.
- Share globals like `siteName` or `authUser` via `$this->view->share([...])` in the base controller or a service provider.

---

## Grading Checklist
- [ ] Project exists at `projects/05/` and runs
- [ ] `src/Support/View.php` implements layout/sections/partials and `e()`
- [ ] `src/Controller.php` renders through the `View` engine
- [ ] Layout at `src/Views/layouts/main.php` yields at least a `content` section
- [ ] Partials present (`partials/head.php`, `partials/nav.php`, `partials/footer.php`)
- [ ] A view (`home.php`) sets a layout and defines a `content` section
- [ ] Output escaping used for dynamic values
- [ ] No external templating libraries used

---

## Submission
Submit the direct URL to your Project 05 folder in your repository (replace YOUR‑USER and repo name):

```
https://github.com/YOUR-USER/YOUR-REPO/blob/main/projects/05/
```

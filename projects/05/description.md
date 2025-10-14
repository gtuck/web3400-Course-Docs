# Project 05 – Introduce Twig and a Base Layout
Use the Twig template engine to render views with a shared base layout. You will install Twig, bootstrap a Twig environment, add a `templates/` directory, and render a minimal page that extends `base.html.twig`.

---

## Overview
You will extend your Project 04 app by:
- Installing `twig/twig` with Composer
- Adding a `templates/` folder and using the provided `base.html.twig`
- Bootstrapping a Twig environment (factory/helper in `src/Support`)
- Rendering a simple page via Twig that extends the base layout
- Passing variables into the template and using blocks/filters

---

## Learning Objectives
- Understand why to use a template engine (security, reuse, clarity)
- Install and bootstrap Twig in a PHP project
- Use templates, blocks, variables, and filters
- Render Twig templates from a controller
- Keep HTML concerns in templates and PHP concerns in controllers/models

---

## Prerequisites
- Completed Project 04 (Composer, autoload, MVC structure, DB helper)
- PHP and Composer installed

---

## Target Structure

```
projects/05/
  composer.json
  public/
    index.php          # same front controller; we’ll call Twig from controllers
  src/
    Router.php         # from P03/P04
    Controller.php     # from P03
    Support/
      TwigFactory.php  # new – returns a configured Twig Environment
    Controllers/
      HomeController.php    # render a Twig view
    Routes/
      index.php        # map GET / → HomeController@index (Twig)
  templates/
    base.html.twig     # provided base layout (copy from Course Docs)
    home.html.twig     # extends base; demonstrates blocks/vars
```

If you are evolving your P04 app, add only the new files/folders and changes below.

---

## Step 1) Copy Project 04 into Project 05

Start from your working Project 04 as the baseline for this project. From your repo root:

```bash
cp -r projects/04 projects/05
```

This copies your MVC structure into `projects/05/`.

---

## Step 2) Create the new files and folders (empty)

From the repo root, after copying P04, create the additional P05 files/folders you’ll need:

```bash
cd projects/05
# Create directories and empty files needed for P05
mkdir -p templates src/Support && \
touch src/Support/TwigFactory.php \
templates/home.html.twig
```

Copy the provided base layout from the Course Docs repo into your new `templates/` folder. Adjust the source path as needed for your environment:

```bash
# From the Course Docs repository root
cp projects/05/base.html.twig YOUR_APP_REPO/projects/05/templates/base.html.twig
```

---

## Step 3) Require Twig with Composer

From `projects/05/` (or your app root):

```bash
composer require twig/twig:^3.0
composer dump-autoload
```

---

## Step 4) Add a Twig environment factory

Create `src/Support/TwigFactory.php` with a static method that returns a configured Twig Environment and registers a minimal `path()` Twig function (so the provided base layout can link to `/` and `/contact`).

```php
<?php

namespace App\Support;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class TwigFactory
{
    private static ?Environment $env = null;

    public static function env(): Environment
    {
        if (self::$env) {
            return self::$env;
        }

        // templates/ folder sits at project root
        $loader = new FilesystemLoader(__DIR__ . '/../../templates');

        $twig = new Environment($loader, [
            'cache' => false,
            'autoescape' => 'html',
        ]);

        // Minimal path() helper so base.html.twig works without a router extension
        $twig->addFunction(new TwigFunction('path', function (string $name, array $params = []): string {
            if ($name === 'home') {
                return '/';
            }
            return '/' . ltrim($name, '/');
        }));

        return self::$env = $twig;
    }
}
```

---

## Step 5) Render Twig from a controller

In your `HomeController` (or create one), call Twig to render a template. Example:

```php
<?php

namespace App\Controllers;

use App\Support\TwigFactory;

class HomeController
{
    public function index(): void
    {
        $twig = TwigFactory::env();
        echo $twig->render('home.html.twig', [
            'siteName' => 'My App',
            'pageTitle' => 'Home',
            'messages' => [],
        ]);
    }
}
```

Register the route in `src/Routes/index.php` so GET `/` dispatches to `HomeController@index`.

---

## Step 6) Create a minimal page that extends the base layout

Edit `templates/home.html.twig` to extend the provided base and override a couple blocks:

```twig
{% raw %}
{% extends 'base.html.twig' %}

{% block title %}{{ pageTitle|default('Home') }} - {{ siteName|default('Site') }}{% endblock %}

{% block page_header %}
  <h1 class="title">Welcome to {{ siteName|default('Site')|e }}</h1>
{% endblock %}

{% block content %}
  <p class="content">This page is rendered with Twig and extends a shared base layout.</p>

  <ul>
    {% for item in ['One','Two','Three'] %}
      <li>{{ item }}</li>
    {% endfor %}
  </ul>
{% endblock %}
{% endraw %}
```

Notes:
- Twig auto-escapes output (HTML) by default per the factory config
- The provided base defines useful blocks: `title`, `meta`, `styles`, `flash`, `page_header`, `content`, `scripts`

---

## Step 7) Convert the Contact view to Twig (example)

Copy the provided example Twig contact template into your app and render it from your controller.

Copy the template into your `templates/` folder:

```bash
# From the Course Docs repository root
cp projects/05/contact.html.twig YOUR_APP_REPO/projects/05/templates/contact.html.twig
```

Update your `ContactController` to use Twig for both GET (show) and POST (submit):

```php
<?php

namespace App\Controllers;

use App\Support\TwigFactory;
use App\Support\Database;

class ContactController
{
    public function show(): void
    {
        echo TwigFactory::env()->render('contact.html.twig', [
            'siteName' => 'My App',
            'pageTitle' => 'Contact',
            'errors' => [],
            'old' => [],
        ]);
    }

    public function submit(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $errors = [];

        if ($name === '') $errors[] = 'Name is required';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required';
        if ($message === '') $errors[] = 'Message is required';

        if ($errors) {
            echo TwigFactory::env()->render('contact.html.twig', [
                'siteName' => 'My App',
                'pageTitle' => 'Contact',
                'errors' => $errors,
                'old' => ['name' => $name, 'email' => $email, 'message' => $message],
            ]);
            return;
        }

        // Insert using PDO helper (from P04)
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('INSERT INTO contact_us (name, email, message) VALUES (:n, :e, :m)');
        $stmt->execute([':n' => $name, ':e' => $email, ':m' => $message]);

        // Render with success message (base layout can show flash messages)
        echo TwigFactory::env()->render('contact.html.twig', [
            'siteName' => 'My App',
            'pageTitle' => 'Contact',
            'errors' => [],
            'old' => [],
            'messages' => [
                ['type' => 'success', 'text' => 'Thanks! We received your message.'],
            ],
        ]);
    }
}
```

Ensure your routes map GET `/contact` → `ContactController@show` and POST `/contact` → `ContactController@submit`.

---

## Step 8) Run and Test

1) Start your local server and open `/` – you should see the page rendered via Twig.
2) Confirm the navbar brand link goes to `/` and the Contact link to `/contact`.
3) Verify variables render correctly and lists/loops work.

---

## Tips, Standards, and Gotchas
- Keep templates in `templates/` and PHP in controllers/models
- Do not echo raw `$_POST`/`$_GET` directly in templates; pass data from controllers
- Twig functions/filters beyond built-ins require registration (we stubbed a simple `path()`)
- Use `|e` when you need explicit escaping; autoescape is enabled

---

## Submission
Submit the direct URL to your Project 05 folder in your repository (replace YOUR-USER and repo name):

```
https://github.com/YOUR-USER/YOUR-REPO/tree/main/projects/05
```

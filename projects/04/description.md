# Project 04 – Dotenv, Database Helper, and Contact Form
Add environment variable support with `vlucas/phpdotenv`, centralize PDO setup in a reusable Database helper, and build a Contact page that safely inserts messages into an existing `contact_us` table.

---

## Overview
You will extend your Project 03 MVC app by:
- Installing `vlucas/phpdotenv` with Composer
- Creating a `.env` file to store DB credentials (and a committed `.env.example`)
- Bootstrapping Dotenv in your front controller
- Implementing a `Database` helper (PDO + settings) that reads from env vars
- Adding a Contact page with a form and POST handler
- Inserting messages into `contact_us` using prepared statements

---

## Learning Objectives
- Configure Composer dependencies and autoloading
- Use environment variables to manage secrets and configuration
- Centralize and reuse PDO connection logic
- Build GET/POST routes in an MVC app
- Validate user input and prevent SQL injection using prepared statements

---

## Prerequisites
- Project 03 structure in place (front controller, router, controllers/views)
- MySQL `contact_us` table exists (from a previous assignment)
  - If missing, use: `projects/01/sql/contact_us.sql`

---

## Target Structure

```
projects/04/
  composer.json
  .env                 # not committed (add to .gitignore)
  .env.example         # committed template
  public/
    index.php          # loads vendor/autoload + Dotenv, then routes
  src/
    Router.php         # from P03
    Controller.php     # from P03
    Support/
      Database.php     # new – central PDO helper
    Controllers/
      ContactController.php
    Routes/
      index.php        # add /contact GET+POST
    Views/
      contact.php      # Contact form + messages
```

If you are evolving your existing P03 app, add only the new files/folders and changes below.

---

## Step 1) Copy Project 03 into Project 04

Start from your working Project 03 as the baseline for this project. From your repo root:

```bash
cp -r projects/03 projects/04
```

This copies your MVC structure (public/, src/, composer.json, etc.) into `projects/04/`.

---

## Step 2) Create the new files and folders (empty)

From the repo root, after copying P03, create the additional P04 files/folders you’ll need:

```bash
cd projects/04
# Create directories and empty files needed for P04
mkdir -p public src/{Controllers,Models,Routes,Views,Support} && \
touch src/Support/Database.php \
src/Controllers/ContactController.php \
src/Views/contact.php \
.env.example
```

---

## Step 3) Require phpdotenv with Composer

From `projects/04/` (or your app root):

```bash
composer require vlucas/phpdotenv
```

Ensure your `composer.json` still maps `App\` to `src/` for PSR‑4 autoloading. If missing, add:

```json
{
  "autoload": {
    "psr-4": { "App\\": "src/" }
  }
}
```

Then regenerate the autoloader:

```bash
composer dump-autoload
```

---

## Step 4) Add .env and .env.example

Create `.env` in your project root (same folder as `composer.json`). Do not commit this file.

Example `.env`:

Add `.env` to your `.gitignore` if it isn’t already ignored.

```
DB_HOST=db
DB_NAME=web3400
DB_USER=web3400
DB_PASS=password
DB_CHARSET=UTF8
```

Commit a `.env.example` with the same keys but placeholder values:

```
DB_HOST=YOUR_DB_HOST
DB_NAME=YOUR_DB_NAME
DB_USER=YOUR_DB_USER
DB_PASS=YOUR_DB_PASS
DB_CHARSET=UTF8
```
---

## Step 5) Bootstrap Dotenv in the front controller

Load autoload and then Dotenv before routing. In `public/index.php`:

```php
<?php
require '../vendor/autoload.php';

// Load environment variables from project root
$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();
$dotenv->required(['DB_HOST','DB_NAME','DB_USER','DB_PASS','DB_CHARSET'])->notEmpty();

// Now bootstrap routes
require '../src/Routes/index.php';
```

Notes:
- `safeLoad()` won’t fatal if `.env` is missing (useful for CI). Use `load()` if you want to require a `.env` file.
- Env values are available via `$_ENV['KEY']` and `$_SERVER['KEY']`.

---

## Step 6) Create a Database helper

Create `src/Support/Database.php` that returns a configured PDO instance using env vars.

```php
<?php
namespace App\Support;

class Database
{
    public static function pdo(): \PDO
    {
        // Read all required settings from environment variables (no hardcoded defaults)
        $host    = $_ENV['DB_HOST'];
        $name    = $_ENV['DB_NAME'];
        $user    = $_ENV['DB_USER'];
        $pass    = $_ENV['DB_PASS'];
        $charset = $_ENV['DB_CHARSET'];

        $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";

        return new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
}
```

Why a helper?
- Centralizes connection details and PDO attributes
- Makes testing and reuse easier
- Keeps controllers/models thin

---

## Step 7) Add ContactController (GET form, POST submit)

Create `src/Controllers/ContactController.php`:

```php
<?php
namespace App\Controllers;

use App\Controller; // base from P03 with render()
use App\Support\Database;

class ContactController extends Controller
{
    public function show()
    {
        $this->render('contact', [
            'errors' => [],
            'old'    => ['name' => '', 'email' => '', 'message' => ''],
            'status' => null,
        ]);
    }

    public function submit()
    {
        $name    = trim($_POST['name']    ?? '');
        $email   = trim($_POST['email']   ?? '');
        $message = trim($_POST['message'] ?? '');

        $errors = [];
        if ($name === '')  { $errors[] = 'Name is required.'; }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email is required.';
        }
        if ($message === '') { $errors[] = 'Message is required.'; }

        if ($errors) {
            return $this->render('contact', [
                'errors' => $errors,
                'old'    => compact('name','email','message'),
                'status' => null,
            ]);
        }

        $pdo = Database::pdo();
        $stmt = $pdo->prepare(
            'INSERT INTO contact_us (name, email, message) VALUES (:name, :email, :message)'
        );
        $stmt->execute([
            ':name'    => $name,
            ':email'   => $email,
            ':message' => $message,
        ]);

        $this->render('contact', [
            'errors' => [],
            'old'    => ['name' => '', 'email' => '', 'message' => ''],
            'status' => 'Thanks! Your message has been received.',
        ]);
    }
}
```

---

## Step 8) Create the Contact view (plain HTML only)

Create `src/Views/contact.php` with no CSS (plain HTML only):

```php
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Contact Us</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>
  <h1>Contact Us</h1>

  <?php if (!empty($errors)): ?>
    <div>
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if (!empty($status)): ?>
    <p><?= htmlspecialchars($status) ?></p>
  <?php endif; ?>

  <form method="post" action="/contact">
    <div>
      <label for="name">Name</label>
      <input id="name" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required />
    </div>
    <div>
      <label for="email">Email</label>
      <input id="email" name="email" type="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required />
    </div>
    <div>
      <label for="message">Message</label>
      <textarea id="message" name="message" rows="5" required><?= htmlspecialchars($old['message'] ?? '') ?></textarea>
    </div>
    <button type="submit">Send Message</button>
  </form>
</body>
</html>
```

---

## Step 9) Register routes

In `src/Routes/index.php`, add GET and POST routes for `/contact`:

```php
<?php
use App\Controllers\ContactController;
use App\Router;

$router = $router ?? new Router();

$router->get('/contact', ContactController::class, 'show');
$router->post('/contact', ContactController::class, 'submit');

// Keep existing routes (e.g., home) and final dispatch
$router->dispatch();
```

Make sure your `public/index.php` requires this routes file after loading Dotenv.

---

## Step 10) Run and Test

From the project root:

```bash
composer dump-autoload
php -S 0.0.0.0:8000 -t public
```

Open `http://localhost:8000/contact` and verify:
- GET shows the Contact form
- Submitting valid data shows a success message
- Data is inserted into `contact_us`
- Invalid inputs re-render the form with error messages and old values

If necessary, confirm your table exists: see `projects/01/sql/contact_us.sql`.

---

## Tips, Standards, and Gotchas
- Never commit `.env`; always commit `.env.example`.
- Keep secrets in env vars; never hardcode credentials in PHP files.
- Use `$_ENV` for config; pass values into your PDO helper.
- Always use prepared statements with bound parameters.
- Escape output in views with `htmlspecialchars()`.
- Validate server-side even if you validate client-side.
- Keep views plain HTML for this project (no CSS). A later project will add styling/templates.
- If you see class not found issues, run `composer dump-autoload`.

---

## Stretch Goals (Optional)
- Add a CSRF token to the form and verify on submit.
- Log submissions to a file for debugging in `APP_ENV=local`.
- Redirect after POST (PRG pattern) to avoid resubmits on refresh.
- Add a simple admin page to list contact messages.

---

## Submission
Submit the direct URL to your Project 04 folder in your repository (replace YOUR-USER and repo name):

```
https://github.com/YOUR-USER/YOUR-REPO/blob/main/projects/04/
```

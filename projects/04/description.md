# Project 04 – Dotenv, Database Helper, BaseModel + Generator, and Contact Form
Add environment variable support with `vlucas/phpdotenv`, centralize PDO setup in a reusable Database helper, implement a lightweight `BaseModel` and code generator, and build a Contact page that saves messages to `contact_us` using the model.

---

## Overview
You will extend your Project 03 MVC app by:
- Installing `vlucas/phpdotenv` with Composer
- Creating a `.env` file to store DB credentials (and a committed `.env.example`)
- Bootstrapping Dotenv in your front controller
- Implementing a `Database` helper (PDO + settings) that reads from env vars
- Implementing a lightweight `BaseModel` and running the model generator
- Using a generated `Contact` model to persist messages
- Adding a Contact page with a form and POST handler
- Inserting messages into `contact_us` using parameterized queries (via model `create()`)

---

## Learning Objectives
- Configure Composer dependencies and autoloading
- Use environment variables to manage secrets and configuration
- Centralize and reuse PDO connection logic
- Build GET/POST routes in an MVC app
- Validate user input and prevent SQL injection using prepared statements
- Implement a lightweight `BaseModel` (CRUD, fillable whitelist)
- Use a generator script to scaffold concrete models (e.g., `Contact`)

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
    Models/
      BaseModel.php    # new – lightweight CRUD helper
      Contact.php      # generated model (from contact_us)
    Controllers/
      ContactController.php
    Routes/
      index.php        # add /contact GET+POST
    Views/
      contact.php      # Contact form + messages
  scripts/
    generate-model.php # CLI to scaffold models from tables
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
mkdir -p public src/{Controllers,Models,Routes,Views,Support} scripts && \
touch src/Support/Database.php \
src/Models/BaseModel.php \
src/Controllers/ContactController.php \
src/Views/contact.php \
scripts/generate-model.php \
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
DB_CHARSET=utf8mb4
```

Commit a `.env.example` with the same keys but placeholder values:

```
DB_HOST=YOUR_DB_HOST
DB_NAME=YOUR_DB_NAME
DB_USER=YOUR_DB_USER
DB_PASS=YOUR_DB_PASS
DB_CHARSET=utf8mb4
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

## Step 7) Add BaseModel + generate Contact model (required)

Add a reusable base model class and generate a concrete `Contact` model from the `contact_us` table.

Included in the reference files for Project 04:
- `src/Models/BaseModel.php` – reusable CRUD on top of `Database::pdo()`
- `scripts/generate-model.php` – creates `src/Models/{Class}.php` from a table

Create `src/Models/BaseModel.php`  

```php
<?php

namespace App\Models;

use App\Support\Database;
use PDO;

abstract class BaseModel
{
    /** @var string Table name (override in subclass) */
    protected static string $table;
    /** @var string Primary key column name */
    protected static string $primaryKey = 'id';
    /** @var array<string> Fillable columns for create/update */
    protected static array $fillable = [];

    protected static function pdo(): PDO
    {
        return Database::pdo();
    }

    protected static function table(): string
    {
        return static::$table;
    }

    protected static function pk(): string
    {
        return static::$primaryKey;
    }

    protected static function sanitize(array $data): array
    {
        return array_intersect_key($data, array_flip(static::$fillable));
    }

    public static function find(int|string $id): ?array
    {
        $sql = 'SELECT * FROM `'.static::table().'` WHERE `'.static::pk().'` = :id LIMIT 1';
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function all(int $limit = 100, int $offset = 0, ?string $orderBy = null): array
    {
        $order = $orderBy ?: '`'.static::pk().'` DESC';
        // If exposing $orderBy from user-input, whitelist columns in callers.
        $sql = 'SELECT * FROM `'.static::table().'` ORDER BY '.$order.' LIMIT :limit OFFSET :offset';
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $data = static::sanitize($data);
        if (!$data) {
            throw new \InvalidArgumentException('No fillable fields provided.');
        }
        $cols = array_keys($data);
        $placeholders = array_map(fn($c) => ':'.$c, $cols);
        $quotedCols = array_map(fn($c) => '`'.$c.'`', $cols);
        $sql = 'INSERT INTO `'.static::table().'` ('.implode(',', $quotedCols).') VALUES ('.implode(',', $placeholders).')';
        $stmt = static::pdo()->prepare($sql);
        foreach ($data as $c => $v) {
            $stmt->bindValue(':'.$c, $v);
        }
        $stmt->execute();
        return (int) static::pdo()->lastInsertId();
    }

    public static function update(int|string $id, array $data): bool
    {
        $data = static::sanitize($data);
        if (!$data) {
            return false;
        }
        $sets = [];
        foreach (array_keys($data) as $c) {
            $sets[] = '`'.$c.'` = :'.$c;
        }
        $sql = 'UPDATE `'.static::table().'` SET '.implode(', ', $sets).' WHERE `'.static::pk().'` = :_id';
        $stmt = static::pdo()->prepare($sql);
        foreach ($data as $c => $v) {
            $stmt->bindValue(':'.$c, $v);
        }
        $stmt->bindValue(':_id', $id);
        return $stmt->execute();
    }

    public static function delete(int|string $id): bool
    {
        $sql = 'DELETE FROM `'.static::table().'` WHERE `'.static::pk().'` = :id';
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}

```

Create `scripts/generate-model.php`  

```php
#!/usr/bin/env php
<?php
// scripts/generate-model.php
// Usage: php scripts/generate-model.php <table_name>

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// Load .env so Database::pdo() has credentials
if (class_exists(\Dotenv\Dotenv::class)) {
    \Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
}

use App\Support\Database;
use PDO;

[$script, $table] = $argv + [null, null];
if (!$table) {
    fwrite(STDERR, "Usage: php scripts/generate-model.php <table>\n");
    exit(1);
}

$pdo = Database::pdo();

$sql = "SELECT COLUMN_NAME, COLUMN_KEY\n          FROM INFORMATION_SCHEMA.COLUMNS\n         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t\n      ORDER BY ORDINAL_POSITION";
$stmt = $pdo->prepare($sql);
$stmt->execute([':t' => $table]);
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$cols) {
    fwrite(STDERR, "Table not found: {$table}\n");
    exit(1);
}

$pk = 'id';
$fillable = [];
foreach ($cols as $col) {
    $name = $col['COLUMN_NAME'];
    if ($col['COLUMN_KEY'] === 'PRI') {
        $pk = $name;
        continue;
    }
    if (in_array($name, ['created_at', 'updated_at', 'deleted_at'], true)) {
        continue;
    }
    $fillable[] = $name;
}

function studly(string $s): string {
    $s = str_replace(['-', '_'], ' ', $s);
    $s = ucwords($s);
    return str_replace(' ', '', $s);
}

function ends_with(string $s, string $suffix): bool {
    $len = strlen($suffix);
    if ($len === 0) return true;
    return substr($s, -$len) === $suffix;
}

function singular(string $s): string {
    if (ends_with($s, 'ies')) return substr($s, 0, -3) . 'y';
    if (ends_with($s, 'ses')) return substr($s, 0, -2); // e.g., analyses -> analysis
    if (ends_with($s, 's')) return substr($s, 0, -1);
    return $s;
}

$class = studly(singular($table));

$template = <<<'PHP'
<?php
namespace App\Models;

final class %CLASS% extends BaseModel
{
    protected static string $table = '%TABLE%';
    protected static string $primaryKey = '%PK%';
    protected static array $fillable = %FILLABLE%;
}

PHP;

$code = str_replace(
    ['%CLASS%', '%TABLE%', '%PK%', '%FILLABLE%'],
    [$class, $table, $pk, var_export($fillable, true)],
    $template
);

$outPath = dirname(__DIR__) . '/src/Models/' . $class . '.php';
@mkdir(dirname($outPath), 0777, true);
file_put_contents($outPath, $code);

echo "Generated model: {$outPath}\n";

```

Generate the `Contact` model and a new `Posts` model (from your project root):

```bash
php scripts/generate-model.php contact_us
php scripts/generate-model.php posts
```

This should create `src/Models/Contact.php` with the correct `$table`, `$primaryKey`, and `$fillable` fields.

Controller usage will call `Contact::create(...)` (see next step).

---

## Step 8) Add ContactController (GET form, POST submit)

Create `src/Controllers/ContactController.php` and persist via the generated `Contact` model:

```php
<?php
namespace App\Controllers;

use App\Controller; // base from P03 with render()
use App\Models\Contact;

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

        // Persist via BaseModel-powered Contact model
        Contact::create([
            'name'    => $name,
            'email'   => $email,
            'message' => $message,
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

## Step 9) Create the Contact view (plain HTML only)

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

## Step 10) Register routes

In `src/Routes/index.php`, add GET and POST routes for `/contact`:

```php
<?php

use App\Controllers\HomeController;
use App\Controllers\ContactController; // new line
use App\Router;

$router = new Router();

$router->get('/', HomeController::class, 'index');
$router->get('/contact', ContactController::class, 'show'); // new line
$router->post('/contact', ContactController::class, 'submit'); // new line

$router->dispatch();

```

Make sure your `public/index.php` requires this routes file after loading Dotenv.

---

## Step 11) Run and Test

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

## BaseModel + Generator (Details)

To keep controllers thin and avoid repeating CRUD, use a simple `BaseModel` and the generator to scaffold concrete model classes from your tables.

BaseModel shape:

```php
namespace App\Models;

use App\Support\Database; use PDO;

abstract class BaseModel {
    protected static string $table;              // set in subclass
    protected static string $primaryKey = 'id';  // override if needed
    protected static array  $fillable = [];      // whitelist for create/update

    public static function find($id): ?array {}
    public static function all($limit=100,$offset=0,$orderBy=null): array {}
    public static function create(array $data): int {}
    public static function update($id, array $data): bool {}
    public static function delete($id): bool {}
}
```

Example model:

```php
final class Contact extends BaseModel {
    protected static string $table = 'contact_us';
    protected static array  $fillable = ['name','email','message'];
}
```

Generator usage (from your project root):

```bash
php scripts/generate-model.php contact_us
```

This will create `src/Models/Contact.php` with the correct `$table`, `$primaryKey`, and `$fillable` (excluding common timestamp fields).

Controller usage:

```php
use App\Models\Contact;

// Create
$id = Contact::create(['name' => $name, 'email' => $email, 'message' => $msg]);

// Read
$row  = Contact::find($id);
$rows = Contact::all(limit: 20);

// Update
Contact::update($id, ['message' => 'Updated message']);

// Delete
Contact::delete($id);
```

Notes:
- Security: `create`/`update` only accept whitelisted `$fillable` fields (server-side validate inputs as usual).
- Ordering: If you allow custom `$orderBy`, whitelist valid columns to avoid SQL injection.
- Transactions: For multi-step operations, wrap logic in a transaction with `$pdo->beginTransaction()` / `commit()`.

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
- When using BaseModel, whitelist allowed columns in `$fillable` and let `create`/`update` sanitize inputs.

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

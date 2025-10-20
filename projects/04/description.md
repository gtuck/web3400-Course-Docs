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
#Database configuration
DB_HOST=db
DB_NAME=web3400
DB_USER=web3400
DB_PASS=password
DB_CHARSET=utf8mb4
```

Commit a `.env.example` with the same keys but placeholder values:

```
#Database configuration
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

/**
 * BaseModel - Abstract base class for all database models
 *
 * Provides a simple Active Record-like pattern for database operations.
 * All model classes should extend this class and define their table name,
 * primary key, and fillable columns.
 *
 * Example usage:
 *
 * class User extends BaseModel {
 *     protected static string $table = 'users';
 *     protected static string $primaryKey = 'id';
 *     protected static array $fillable = ['name', 'email', 'password'];
 * }
 *
 * // Find a user by ID
 * $user = User::find(1);
 *
 * // Get all users
 * $users = User::all();
 *
 * // Create a new user
 * $id = User::create(['name' => 'John', 'email' => 'john@example.com']);
 *
 * // Update a user
 * User::update(1, ['name' => 'Jane']);
 *
 * // Delete a user
 * User::delete(1);
 */
abstract class BaseModel
{
    /** @var string Table name (override in subclass) */
    protected static string $table;

    /** @var string Primary key column name (default: 'id') */
    protected static string $primaryKey = 'id';

    /** @var array<string> Fillable columns for create/update operations (whitelist for mass assignment protection) */
    protected static array $fillable = [];

    /**
     * Get the PDO database connection instance
     *
     * @return PDO The database connection object
     *
     * Example:
     * $pdo = static::pdo();
     * $stmt = $pdo->prepare('SELECT * FROM users WHERE active = 1');
     */
    protected static function pdo(): PDO
    {
        return Database::pdo();
    }

    /**
     * Get the table name for this model
     *
     * @return string The database table name
     *
     * Example:
     * $tableName = static::table(); // Returns 'users' for User model
     */
    protected static function table(): string
    {
        return static::$table;
    }

    /**
     * Get the primary key column name for this model
     *
     * @return string The primary key column name (typically 'id')
     *
     * Example:
     * $pk = static::pk(); // Returns 'id' by default
     */
    protected static function pk(): string
    {
        return static::$primaryKey;
    }

    /**
     * Sanitize input data to only include fillable columns
     *
     * This provides mass assignment protection by filtering out any
     * columns that are not explicitly defined in the $fillable array.
     *
     * @param array $data Raw input data (e.g., from $_POST)
     * @return array Sanitized data containing only fillable columns
     *
     * Example:
     * // If $fillable = ['name', 'email']
     * $input = ['name' => 'John', 'email' => 'john@example.com', 'is_admin' => 1];
     * $safe = static::sanitize($input);
     * // Result: ['name' => 'John', 'email' => 'john@example.com']
     * // Note: 'is_admin' is removed because it's not in $fillable
     */
    protected static function sanitize(array $data): array
    {
        return array_intersect_key($data, array_flip(static::$fillable));
    }

    /**
     * Find a single record by its primary key
     *
     * Retrieves one row from the database matching the given ID.
     * Returns null if no record is found.
     *
     * @param int|string $id The primary key value to search for
     * @return array|null Associative array of the record, or null if not found
     *
     * Example:
     * // Find user with ID 5
     * $user = User::find(5);
     * if ($user) {
     *     echo $user['name']; // Access column values
     *     echo $user['email'];
     * } else {
     *     echo "User not found";
     * }
     *
     * // Also works with string IDs (e.g., UUIDs)
     * $item = Item::find('550e8400-e29b-41d4-a716-446655440000');
     */
    public static function find(int|string $id): ?array
    {
        $sql = 'SELECT * FROM `'.static::table().'` WHERE `'.static::pk().'` = :id LIMIT 1';
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Retrieve all records from the table with pagination and ordering
     *
     * Fetches multiple records with support for limiting, offsetting, and custom ordering.
     * By default, returns up to 100 records ordered by primary key descending (newest first).
     *
     * SECURITY NOTE: If exposing $orderBy from user input, always whitelist allowed columns
     * in your calling code to prevent SQL injection.
     *
     * @param int $limit Maximum number of records to return (default: 100)
     * @param int $offset Number of records to skip (for pagination, default: 0)
     * @param string|null $orderBy Custom ORDER BY clause (default: primary key DESC)
     * @return array Array of associative arrays, each representing a record
     *
     * Example:
     * // Get the first 10 users (default newest first)
     * $users = User::all(10);
     *
     * // Get the next 10 users (pagination)
     * $users = User::all(10, 10); // Skip first 10, get next 10
     *
     * // Custom ordering by name ascending
     * $users = User::all(50, 0, '`name` ASC');
     *
     * // Page 3 of results (20 per page)
     * $page = 3;
     * $perPage = 20;
     * $offset = ($page - 1) * $perPage;
     * $users = User::all($perPage, $offset);
     *
     * // Iterate through results
     * foreach ($users as $user) {
     *     echo $user['name'] . '<br>';
     * }
     */
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

    /**
     * Create a new record in the database
     *
     * Inserts a new row with the provided data. Only columns listed in the
     * $fillable array will be inserted (mass assignment protection).
     * Returns the auto-generated ID of the newly created record.
     *
     * @param array $data Associative array of column => value pairs to insert
     * @return int The ID of the newly created record (from lastInsertId)
     * @throws \InvalidArgumentException If no fillable fields are provided
     *
     * Example:
     * // Create a new user
     * $userId = User::create([
     *     'name' => 'John Doe',
     *     'email' => 'john@example.com',
     *     'password' => password_hash('secret123', PASSWORD_DEFAULT)
     * ]);
     * echo "New user created with ID: $userId";
     *
     * // Using data from a form submission
     * $newId = User::create($_POST); // Only fillable fields will be used
     *
     * // Handle potential errors
     * try {
     *     $productId = Product::create([
     *         'name' => 'Widget',
     *         'price' => 29.99,
     *         'stock' => 100
     *     ]);
     * } catch (\InvalidArgumentException $e) {
     *     echo "Error: " . $e->getMessage();
     * }
     */
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

    /**
     * Update an existing record by its primary key
     *
     * Updates the specified record with new data. Only columns listed in the
     * $fillable array will be updated (mass assignment protection).
     * Returns true if the update query executed successfully, false if no
     * fillable fields were provided.
     *
     * @param int|string $id The primary key value of the record to update
     * @param array $data Associative array of column => value pairs to update
     * @return bool True if update executed successfully, false if no fillable data
     *
     * Example:
     * // Update a user's email
     * $success = User::update(5, ['email' => 'newemail@example.com']);
     * if ($success) {
     *     echo "User updated successfully";
     * }
     *
     * // Update multiple columns
     * User::update(10, [
     *     'name' => 'Jane Smith',
     *     'email' => 'jane@example.com',
     *     'status' => 'active'
     * ]);
     *
     * // Update from form data (only fillable fields will be used)
     * $userId = 3;
     * User::update($userId, $_POST);
     *
     * // Partial updates are allowed
     * Product::update(15, ['stock' => 50]); // Only updates stock column
     *
     * // Check if update affected any rows (not supported by default, but can be extended)
     * $result = User::update(99, ['name' => 'New Name']);
     * // $result is true if query executed, even if no rows matched ID 99
     */
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

    /**
     * Delete a record by its primary key
     *
     * Permanently removes the specified record from the database.
     * Returns true if the deletion query executed successfully.
     *
     * WARNING: This operation is irreversible. Consider implementing soft deletes
     * (e.g., a 'deleted_at' timestamp column) for recoverable deletions.
     *
     * @param int|string $id The primary key value of the record to delete
     * @return bool True if deletion executed successfully
     *
     * Example:
     * // Delete a user by ID
     * $success = User::delete(5);
     * if ($success) {
     *     echo "User deleted successfully";
     * } else {
     *     echo "Failed to delete user";
     * }
     *
     * // Delete with confirmation
     * $userId = 10;
     * $user = User::find($userId);
     * if ($user && confirm("Delete user {$user['name']}?")) {
     *     User::delete($userId);
     * }
     *
     * // Batch deletion (manual loop needed)
     * $idsToDelete = [1, 2, 3, 4, 5];
     * foreach ($idsToDelete as $id) {
     *     User::delete($id);
     * }
     *
     * // NOTE: The return value indicates query success, not whether
     * // a row was actually deleted. Returns true even if ID doesn't exist.
     * $result = User::delete(99999); // Returns true even if user 99999 doesn't exist
     */
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
/**
 * Model Generator Script
 *
 * PURPOSE:
 * CLI tool to automatically generate model classes from database tables.
 * Introspects table structure and creates a properly formatted model file.
 *
 * USAGE:
 * php scripts/generate-model.php <table_name>
 *
 * EXAMPLES:
 * php scripts/generate-model.php users
 * php scripts/generate-model.php blog_posts
 * php scripts/generate-model.php contact_us
 *
 * FEATURES:
 * - Auto-detects primary key
 * - Excludes timestamp columns (created_at, updated_at, deleted_at)
 * - Converts table names to singular class names (posts → Post)
 * - Generates clean, properly formatted model files with documentation
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// Load .env so Database::pdo() has credentials
if (class_exists(\Dotenv\Dotenv::class)) {
    \Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
}

use App\Support\Database;
use PDO;

// Parse command-line arguments
[$script, $table] = $argv + [null, null];
if (!$table) {
    fwrite(STDERR, "Usage: php scripts/generate-model.php <table>\n");
    exit(1);
}

// Connect to database
$pdo = Database::pdo();

// Query table structure from INFORMATION_SCHEMA
$sql = "SELECT COLUMN_NAME, COLUMN_KEY
          FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t
      ORDER BY ORDINAL_POSITION";
$stmt = $pdo->prepare($sql);
$stmt->execute([':t' => $table]);
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Validate table exists
if (!$cols) {
    fwrite(STDERR, "Table not found: {$table}\n");
    exit(1);
}

// Extract primary key and fillable columns
$pk = 'id';
$fillable = [];
foreach ($cols as $col) {
    $name = $col['COLUMN_NAME'];

    // Identify primary key
    if ($col['COLUMN_KEY'] === 'PRI') {
        $pk = $name;
        continue;
    }

    // Exclude timestamp columns (auto-managed by database)
    if (in_array($name, ['created_at', 'updated_at', 'deleted_at'], true)) {
        continue;
    }

    $fillable[] = $name;
}

/**
 * Convert snake_case or kebab-case to StudlyCase
 * Example: blog_posts → BlogPosts, user-roles → UserRoles
 */
function studly(string $s): string
{
    $s = str_replace(['-', '_'], ' ', $s);
    $s = ucwords($s);
    return str_replace(' ', '', $s);
}

/**
 * Check if string ends with a suffix
 */
function ends_with(string $s, string $suffix): bool
{
    $len = strlen($suffix);
    if ($len === 0) return true;
    return substr($s, -$len) === $suffix;
}

/**
 * Convert plural table name to singular class name
 * Examples: posts → post, categories → category, analyses → analysis
 */
function singular(string $s): string
{
    if (ends_with($s, 'ies')) return substr($s, 0, -3) . 'y';
    if (ends_with($s, 'ses')) return substr($s, 0, -2);
    if (ends_with($s, 's')) return substr($s, 0, -1);
    return $s;
}

/**
 * Format array as clean PHP array literal syntax
 * Example: ['name', 'email', 'message']
 */
function format_array(array $items): string
{
    if (empty($items)) {
        return '[]';
    }

    $quoted = array_map(fn($item) => "'{$item}'", $items);
    return '[' . implode(', ', $quoted) . ']';
}

// Generate class name from table name
$class = studly(singular($table));

// Generate fillable array with clean syntax
$fillableString = format_array($fillable);

// Model template with documentation
$template = <<<'PHP'
<?php
/**
 * %CLASS% Model
 *
 * PURPOSE:
 * Represents records in the '%TABLE%' database table.
 * Provides CRUD operations via ActiveRecord pattern.
 *
 * DATABASE TABLE: %TABLE%
 * PRIMARY KEY: %PK%
 *
 * USAGE EXAMPLES:
 *
 * // Create a new record
 * $id = %CLASS%::create([
 *     // Add your fields here
 * ]);
 *
 * // Find a specific record
 * $record = %CLASS%::find(1);
 *
 * // Get all records
 * $records = %CLASS%::all(limit: 10, orderBy: 'created_at DESC');
 *
 * // Update a record
 * %CLASS%::update(1, [
 *     // Updated fields
 * ]);
 *
 * // Delete a record
 * %CLASS%::delete(1);
 */

namespace App\Models;

final class %CLASS% extends BaseModel
{
    /** @var string Database table name */
    protected static string $table = '%TABLE%';

    /** @var string Primary key column */
    protected static string $primaryKey = '%PK%';

    /** @var array<string> Columns allowed for mass assignment */
    protected static array $fillable = %FILLABLE%;
}

PHP;

// Replace placeholders with actual values
$code = str_replace(
    ['%CLASS%', '%TABLE%', '%PK%', '%FILLABLE%'],
    [$class, $table, $pk, $fillableString],
    $template
);

// Write model file
$outPath = dirname(__DIR__) . '/src/Models/' . $class . '.php';
@mkdir(dirname($outPath), 0777, true);
file_put_contents($outPath, $code);

echo "Generated model: {$outPath}\n";
echo "Class name: {$class}\n";
echo "Table: {$table}\n";
echo "Primary key: {$pk}\n";
echo "Fillable fields: " . implode(', ', $fillable) . "\n";
```

Generate the `Contact` model (from your project root):

```bash
php scripts/generate-model.php contact_us
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

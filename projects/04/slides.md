---
theme: default
title: Dotenv, DB Helper, and Contact Form
info: |
  Use phpdotenv for configuration, centralize PDO, and add a secure contact flow
layout: cover
highlighter: shiki
lineNumbers: true
drawings:
  persist: false
transition: slide-left
mdc: true
download: true
exportFilename: dotenv-db-helper-contact
class: text-center
---

# Dotenv, DB Helper, and Contact Form

Secure config • Reusable PDO • Prepared statements

---
layout: two-cols
---

# Recap (Project 03)

You built:
- Front controller (`public/index.php`)
- Simple Router + routes file
- PSR-4 autoload + namespaces
- Controllers, Models, Views

::right::

# Today (Project 04)

Add:
- `vlucas/phpdotenv` for config
- `.env` and `.env.example`
- `Database` helper (PDO)
- Contact form (GET/POST)
- Prepared statements (INSERT)

---

# Why Dotenv?

Problems it solves:
- Keeps secrets out of code and VCS
- Environment-specific config (dev, prod)
- Simple key/value UX for students

Where it lives:
- `.env` next to `composer.json`
- Load early in `public/index.php`

---

# Install phpdotenv

Commands:
```bash
composer require vlucas/phpdotenv
composer dump-autoload
```

Bootstrapping:
```php
require '../vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();
$dotenv->required(['DB_HOST','DB_NAME','DB_USER','DB_PASS','DB_CHARSET'])->notEmpty();
```

Access:
```php
$host = $_ENV['DB_HOST'];
```

---

# .env vs .env.example

- `.env` – untracked, developer-specific
- `.env.example` – committed template keys
- Add `.env` to `.gitignore`

Example keys:
```
DB_HOST=db
DB_NAME=web3400
DB_USER=web3400
DB_PASS=password
DB_CHARSET=UTF8
```

---

# Database Helper (Design)

Goals:
- One place to configure PDO
- Read from env vars
- Reuse across controllers/models

Signature:
```php
class Database { public static function pdo(): \PDO; }
```

Attributes:
- `ERRMODE_EXCEPTION`
- `DEFAULT_FETCH_MODE => FETCH_ASSOC`
- `EMULATE_PREPARES => false`

---

# Database Helper (Code)

```php
namespace App\Support;

class Database {
  public static function pdo(): \PDO {
    $dsn = sprintf(
      'mysql:host=%s;dbname=%s;charset=%s',
      $_ENV['DB_HOST'],
      $_ENV['DB_NAME'],
      $_ENV['DB_CHARSET']
    );
    return new \PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
      \PDO::ATTR_EMULATE_PREPARES => false,
    ]);
  }
}
```

---

# Contact Page (Flow)

Routes:
- GET `/contact` → show form
- POST `/contact` → validate + insert

Table:
- `contact_us(id, name, email, message, submitted_at)`

Key skills:
- Server-side validation
- Prepared INSERT
- User feedback (success/error)

---

# Controller (Sketch)

```php
class ContactController extends Controller {
  public function show() { $this->render('contact', ['errors'=>[], 'old'=>[]]); }
  public function submit() {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $msg = trim($_POST['message'] ?? '');
    $errors = [];
    if ($name==='') $errors[]='Name required';
    if ($email==='' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[]='Valid email required';
    if ($msg==='') $errors[]='Message required';
    if ($errors) return $this->render('contact', ['errors'=>$errors,'old'=>compact('name','email','msg')]);
    $pdo = Database::pdo();
    $pdo->prepare('INSERT INTO contact_us (name,email,message) VALUES (:n,:e,:m)')
        ->execute([':n'=>$name, ':e'=>$email, ':m'=>$msg]);
    $this->render('contact', ['errors'=>[], 'old'=>[], 'status'=>'Thanks!']);
  }
}
```

---

# View (Essentials)

```php
<form method="post" action="/contact">
  <label>Name <input name="name" required></label>
  <label>Email <input name="email" type="email" required></label>
  <label>Message <textarea name="message" required></textarea></label>
  <button type="submit">Send</button>
</form>
```

Notes:
- Echo old values after validation errors
- Escape output with `htmlspecialchars()`

---

# Prepared Statements 101

Why:
- Prevent SQL injection
- Correctly binds types and escapes inputs

Pattern:
```php
$stmt = $pdo->prepare('INSERT ... VALUES (:a, :b, :c)');
$stmt->execute([':a'=>$a, ':b'=>$b, ':c'=>$c]);
```

Avoid:
- String interpolation in SQL
- Building SQL with user input directly

---

# Demo Plan

1) Install `vlucas/phpdotenv`
2) Add `.env` + bootstrap
3) Add `Database::pdo()`
4) Build `ContactController` + routes
5) Create `contact.php` view
6) Test GET + POST; verify DB insert

---

# Common Pitfalls
- Forgetting to ignore `.env`
- Loading Dotenv after using env vars
- Wrong DSN or charset
- Not using prepared statements
- Missing HTML escaping in view

---

# Q&A / Next Steps

- PRG pattern (redirect after POST)
- CSRF token basics
- Listing messages for admin

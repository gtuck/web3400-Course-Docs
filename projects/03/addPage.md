# Project 03: Add a "Contact Us" Page (MVC + Router)

This tutorial walks you through adding a new page to your Project 03 app: a Contact Us form that saves submissions to the database using a prepared statement:

INSERT INTO contact_us (name, email, message) VALUES (:name, :email, :message)

You will:
- Create a database table for submissions
- Register GET/POST routes (`/contact`)
- Add a `ContactController` with `index()` and `store()` actions
- Add a `Contact` model that inserts the data with PDO prepared statements
- Build a view with the HTML form and success/error display

The steps below align with the rubric principles (PSR-4 autoloading, namespaces, router, base controller render helper, PDO model).

---

## 1) Prerequisites

- Composer autoload configured with PSR-4 (e.g., `"App\\": "src/"`).
- A base controller (e.g., `App\Controller`) with a `render($view, array $data = [])` helper.
- A minimal router that supports `GET` and `POST` and can dispatch controller actions.
- A working PDO connection pattern (via an existing Database/Model helper or similar).

If your app differs, adjust names/paths accordingly.

---

## 2) Create the `contact_us` table

Run one of the following, depending on your database.

MySQL/MariaDB:

```sql
CREATE TABLE IF NOT EXISTS contact_us (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);
```

---

## 3) Register routes

In `src/Routes/index.php`, register a GET to show the form and a POST to handle submissions. Adjust import/namespaces to your router style.

```php
<?php

use App\Controllers\ContactController;

// Show the form
$router->get('/contact', [ContactController::class, 'index']);

// Handle form POST
$router->post('/contact', [ContactController::class, 'store']);
```

---

## 4) Create the model: `src/Models/Contact.php`

This model encapsulates the INSERT logic using a prepared statement with named parameters.

```php
<?php

namespace App\Models;

use PDO;

class Contact
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(string $name, string $email, string $message): bool
    {
        $sql = 'INSERT INTO contact_us (name, email, message) VALUES (:name, :email, :message)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':message' => $message,
        ]);
    }
}
```

Obtaining a PDO instance:
- If your project already has a `Database` helper (e.g., `Database::pdo()`), reuse it in the controller.
- Otherwise, construct `PDO` in the controller using your DSN/env config.

---

## 5) Create the controller: `src/Controllers/ContactController.php`

This controller renders the form and processes submissions. It shows simple validation and echoes errors back to the view. Replace the PDO creation with your app’s preferred approach if you already centralize DB config.

```php
<?php

namespace App\Controllers;

use App\Controller; // your base controller with render()
use App\Models\Contact;
use PDO;

class ContactController extends Controller
{
    public function index(): void
    {
        $sent = isset($_GET['sent']);
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];

        // Clear flash data
        unset($_SESSION['errors'], $_SESSION['old']);

        $this->render('contact/index', [
            'sent' => $sent,
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function store(): void
    {
        // Basic validation
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        $errors = [];
        if ($name === '') { $errors['name'] = 'Name is required.'; }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors['email'] = 'Valid email is required.'; }
        if ($message === '') { $errors['message'] = 'Message is required.'; }

        if ($errors) {
            // Flash errors + old input and redirect back to the form
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['name' => $name, 'email' => $email, 'message' => $message];
            header('Location: /contact');
            exit;
        }

        // Obtain PDO (replace with your existing PDO factory/DI if available)
        $dsn = getenv('DB_DSN') ?: 'mysql:host=localhost;dbname=app;charset=utf8mb4';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $contact = new Contact($pdo);
        $contact->create($name, $email, $message);

        // Redirect with a success flag
        header('Location: /contact?sent=1');
        exit;
    }
}
```

Notes:
- If you have CSRF protection middleware, include a hidden token and validate it in `store()`.
- If you already have a `Database` or `Model` base with a shared PDO, prefer that over instantiating directly here.

---

## 6) Create the view: `views/contact/index.php`

This view shows the form, any validation errors, and a success message after redirect.

```php
<?php
// $sent (bool), $errors (array), $old (array) provided by the controller
function e(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>

<main>
  <h1>Contact Us</h1>

  <?php if (!empty($sent)): ?>
    <p class="notice">Thanks! Your message has been sent.</p>
  <?php endif; ?>

  <form method="post" action="/contact" novalidate>
    <div>
      <label for="name">Name</label>
      <input id="name" name="name" type="text" value="<?= isset($old['name']) ? e($old['name']) : '' ?>" required>
      <?php if (!empty($errors['name'])): ?><small class="error"><?= e($errors['name']) ?></small><?php endif; ?>
    </div>

    <div>
      <label for="email">Email</label>
      <input id="email" name="email" type="email" value="<?= isset($old['email']) ? e($old['email']) : '' ?>" required>
      <?php if (!empty($errors['email'])): ?><small class="error"><?= e($errors['email']) ?></small><?php endif; ?>
    </div>

    <div>
      <label for="message">Message</label>
      <textarea id="message" name="message" rows="6" required><?= isset($old['message']) ? e($old['message']) : '' ?></textarea>
      <?php if (!empty($errors['message'])): ?><small class="error"><?= e($errors['message']) ?></small><?php endif; ?>
    </div>

    <button type="submit">Send</button>
  </form>
</main>
```

Adjust the include path based on how your base `render()` locates views (e.g., `views/contact/index.php`).

---

## 7) Verify end-to-end

- Start your PHP server and navigate to `/contact`.
- Submit the form with valid values.
- Confirm you’re redirected to `/contact?sent=1` and see the success message.
- Check the database: a new row should exist in `contact_us` with your input.

---

## 8) Troubleshooting

- If you get a 404, ensure the routes are registered before dispatch and the router parses the path without query strings.
- If insertion fails, check PDO DSN, credentials, and that the `contact_us` table exists.
- If you see PHP notices, verify namespaces match PSR-4 paths and that the `render()` helper includes the correct view.

---

## 9) Next steps (nice-to-haves)

- Add CSRF protection and rate limiting (or a simple honeypot field).
- Add server-side email notification after storing the message.
- Add basic styling and client-side validation for better UX.


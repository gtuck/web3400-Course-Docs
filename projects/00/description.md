# Project 00 — Project Configuration, Security, HTML Form Processing & Database Connections

This project sets up a basic PHP web project using **PDO** for database access and a simple **template system** (`head.php`, `nav.php`, `footer.php`). You’ll also build a secure **Contact Us** form that saves messages to the database and displays user feedback with session‑based flash messages (dismissed via **BulmaJS**).

---

## **Setup Steps**

### **1. Copy Assignment 04 to the `projects/00` Folder**
```bash
cd your-repo-root
mkdir -p projects/00
cp -R assignments/04/* projects/00/
```
Commit and push changes:
```bash
git add projects/00
git commit -m "Project 00: seed from A04"
git push origin main
```

---

### **2. Create `config.php`**
Centralize site settings, database connection (PDO), and flash messaging.

```php
<?php
// ---------- Site Variables ----------
$siteName     = "My PHP Site";
$contactEmail = "contact@example.com";
$contactPhone = "123-456-7890";

// ---------- Session & Flash Messages ----------
session_start();

if (!isset($_SESSION['messages'])) {
    $_SESSION['messages'] = [];
}
function flash($text, $type = 'is-info') {
    $_SESSION['messages'][] = ['type' => $type, 'text' => $text];
}

// ---------- Database Connection (PDO) ----------
try {
    $host = 'db';
    $dbname = 'web3400';
    $username = 'web3400';
    $password = 'password';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=UTF8";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Could not connect to database. Please try again later.");
}
?>
```

---

### **3. Create `.htaccess`**

```apache
# Show PHP errors during development (remove/disable in production)
php_flag display_errors on

# Deny direct access to config.php (Apache 2.4+ syntax)
<Files "config.php">
    Require all denied
</Files>
```

---

### **4. Create the Database Table**

Log into **phpMyAdmin** and run:

```sql
CREATE TABLE IF NOT EXISTS contact_us (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  message TEXT,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### **5. Create `contact.php` (Form + Logic)**

#### A) PHP Logic at Top of File
```php
<?php
require __DIR__ . '/config.php';

$old = ['name' => '', 'email' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $old = ['name' => $name, 'email' => $email, 'message' => $message];

    $errors = [];
    if ($name === '' || mb_strlen($name) > 255) $errors[] = "Please provide your name.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please provide a valid email.";
    if ($message === '') $errors[] = "Message is required.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO contact_us (name, email, message) VALUES (:name, :email, :message)");
        $stmt->execute([':name'=>$name, ':email'=>$email, ':message'=>$message]);
        flash('Thank you for contacting us!', 'is-success');
        header('Location: contact.php');
        exit;
    } else {
        foreach ($errors as $err) flash($err, 'is-warning');
        header('Location: contact.php');
        exit;
    }
}
?>
```

#### B) Page Shell with Templates
```php
<?php
// filepath: projects/00/contact.php
require __DIR__ . '/config.php';
$pageTitle = 'Contact - ' . ($siteName ?? 'Site');
?>
<?php require __DIR__ . '/templates/head.php'; ?>
<?php require __DIR__ . '/templates/nav.php'; ?>
<?php require __DIR__ . '/templates/flash.php'; ?>

<!-- Contact Form (see HTML below) -->

<?php require __DIR__ . '/templates/footer.php'; ?>
```

#### C) Contact Form HTML
```php
<section class="section">
  <h1 class="title">Contact Us</h1>
  <form class="box" action="contact.php" method="post" novalidate>
    <div class="field">
      <label class="label">Your Name</label>
      <div class="control">
        <input class="input" type="text" name="name" value="<?= htmlspecialchars($old['name'], ENT_QUOTES) ?>" required>
      </div>
    </div>
    <div class="field">
      <label class="label">Your Email</label>
      <div class="control">
        <input class="input" type="email" name="email" value="<?= htmlspecialchars($old['email'], ENT_QUOTES) ?>" required>
      </div>
    </div>
    <div class="field">
      <label class="label">Your Message</label>
      <div class="control">
        <textarea class="textarea" name="message" required><?= htmlspecialchars($old['message'], ENT_QUOTES) ?></textarea>
      </div>
    </div>
    <div class="field">
      <div class="control">
        <button class="button is-primary">Send Message</button>
      </div>
    </div>
  </form>
  <!-- You can include additional contact info here -->
  <p class="is-size-7 has-text-grey">Email: <?= htmlspecialchars($contactEmail, ENT_QUOTES) ?> | Phone: <?= htmlspecialchars($contactPhone, ENT_QUOTES) ?></p>
  </section>
```

---

### **6. Update Templates**

#### `index.php`
```php
<?php
// filepath: projects/00/index.php
require __DIR__ . '/config.php';
$pageTitle = 'Home - ' . ($siteName ?? 'Site');
?>
<?php require __DIR__ . '/templates/head.php'; ?>
<?php require __DIR__ . '/templates/nav.php'; ?>
<?php require __DIR__ . '/templates/flash.php'; ?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
  <h1 class="title">Welcome to <?= htmlspecialchars($siteName ?? 'My PHP Site', ENT_QUOTES) ?></h1>
  <h2 class="subtitle">This is a subtitle</h2>
  <p><a class="button is-link" href="contact.php">Go to Contact</a></p>
  </section>
<!-- END YOUR CONTENT -->

<?php require __DIR__ . '/templates/footer.php'; ?>
```

#### `templates/head.php`
```php
<?php
// filepath: projects/00/templates/head.php
$pageTitle = $pageTitle ?? ($siteName ?? 'Site Title');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Project 00 - Template system, PDO, contact form">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></title>

  <!-- Bulma & Assets (match A04 versions) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
  <script src="https://cdn.jsdelivr.net/npm/@vizuaalog/bulmajs@0.12/dist/bulma.min.js" defer></script>
</head>
<body class="has-navbar-fixed-top">
```

#### `templates/nav.php`
```php
<?php
// filepath: projects/00/templates/nav.php
?>
<!-- BEGIN PAGE HEADER -->
<header class="container">

  <!-- BEGIN MAIN NAV -->
  <nav class="navbar is-fixed-top is-spaced has-shadow is-light" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
      <a class="navbar-item" href="index.php">
        <span class="icon-text">
          <span class="icon"><i class="fas fa-code"></i></span>
          <span><?= htmlspecialchars($siteName ?? 'My PHP Site', ENT_QUOTES) ?></span>
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
        <!-- Admin menu will go here in the future -->
      </div>
      <div class="navbar-end">
        <a class="navbar-item" href="contact.php">Contact</a>
      </div>
    </div>
  </nav>
  <!-- END MAIN NAV -->

  <section class="block">&nbsp;</section>

  <!-- BEGIN HERO -->
  <section class="hero is-primary">
    <div class="hero-body">
      <p class="title">Hero title</p>
      <p class="subtitle">Hero subtitle</p>
    </div>
  </section>
  <!-- END HERO -->
</header>
<!-- END PAGE HEADER -->

<!-- BEGIN MAIN PAGE CONTENT -->
<main class="container">
```

#### `templates/flash.php`
```php
<?php
// filepath: projects/00/templates/flash.php
if (!empty($_SESSION['messages'])):
?>
  <section class="section">
    <?php foreach ($_SESSION['messages'] as $m): ?>
      <div class="notification <?= htmlspecialchars($m['type'], ENT_QUOTES) ?>">
        <button class="delete" data-bulma="notification"></button>
        <?= htmlspecialchars($m['text'], ENT_QUOTES) ?>
      </div>
    <?php endforeach; $_SESSION['messages'] = []; ?>
  </section>
<?php endif; ?>
```

#### `templates/footer.php`
```php
<?php
// filepath: projects/00/templates/footer.php
$year = date('Y');
?>
  </main>
  <!-- END MAIN PAGE CONTENT -->

  <!-- BEGIN PAGE FOOTER -->
  <footer class="footer">
    <div class="content has-text-centered">
      <p>&copy; <?= $year ?> <?= htmlspecialchars($siteName ?? 'My PHP Site', ENT_QUOTES) ?>. Built with Bulma & PHP.</p>
    </div>
  </footer>
  <!-- END PAGE FOOTER -->
</body>
</html>
```

---

### **7. Test Locally (Optional Built-in Server)**
From repository root:

```bash
php -S 0.0.0.0:8080
```

Browse:
- http://localhost:8080/projects/00/index.php
- http://localhost:8080/projects/00/contact.php

---

### **8. Commit and Push Final Changes**
```bash
git add projects/00
git commit -m "Project 00: config, PDO, contact form, flash messages"
git push origin main
```

---

### **9. Submit the Project**
Submit the direct URL (replace YOUR-USER):
```
https://github.com/YOUR-USER/web3400-fall25/blob/main/projects/00/index.php
```

Open in a private/incognito window to confirm accessibility.

---

## Production Notes
- Don’t commit real credentials.
- Disable detailed error display in production.
- Validate and sanitize all inputs.
- Escape user content on output (`htmlspecialchars`).

---

## Grading Checklist
- [ ] Project copied to `projects/00/` (not nested) and pushed.
- [ ] `config.php` loads; session + PDO configured.
- [ ] `.htaccess` present and denies `config.php` access.
- [ ] `contact_us` table created in database.
- [ ] `contact.php` validates input and uses prepared statements.
- [ ] `templates/flash.php` present; flash messages display and dismiss via BulmaJS.
- [ ] Templates wired correctly in `templates/` (`head.php`, `nav.php`, `footer.php`).
- [ ] No duplicate DOCTYPE or `<html>` tags; include order: head → nav → flash → content → footer.
- [ ] `$pageTitle` used and renders; assets consistent with A04 (Bulma 1.0.4, FontAwesome 5.15.4, BulmaJS 0.12).
- [ ] Any legacy `index.html` removed; `index.php` is the entry.


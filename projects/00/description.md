# Project 00 — Project Configuration, Security, HTML Form Processing & Database Connections

This project sets up a basic PHP web project using **PDO** for database access and a simple **template system** (`head.php`, `nav.php`, `footer.php`). You’ll also build a secure **Contact Us** form that saves messages to the database and displays user feedback with session‑based flash messages (dismissed via **BulmaJS**).

---

## **Setup Steps**

### **1. Copy Assignment 04 to the `projects/00` Folder**
```bash
cd your-repo-root
cp -r assignments/04 projects/00
```
Commit and push changes:
```bash
git add *
git commit -m "Created project 00 folder"
git push
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

#### B) Contact Form HTML
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
</section>
```

---

### **6. Update Templates**

#### `index.php`
```php
<?php require __DIR__ . '/config.php'; ?>
<?php require __DIR__ . '/head.php'; ?>
<?php require __DIR__ . '/nav.php'; ?>

<section class="section">
  <h1 class="title">Welcome to <?= htmlspecialchars($siteName, ENT_QUOTES) ?></h1>
</section>

<?php require __DIR__ . '/footer.php'; ?>
```

#### `head.php`
```html
<title><?= htmlspecialchars($siteName, ENT_QUOTES) ?></title>
```

#### `footer.php`
```html
<footer class="footer">
  <div class="content has-text-centered">
    <strong><?= htmlspecialchars($siteName, ENT_QUOTES) ?></strong> | &copy; <?= date('Y') ?>
  </div>
</footer>
```

#### `nav.php`
```php
<nav class="navbar">
  <div class="navbar-brand">
    <a class="navbar-item" href="index.php"><?= htmlspecialchars($siteName, ENT_QUOTES) ?></a>
  </div>
  <div class="navbar-end">
    <a class="navbar-item" href="contact.php">Contact</a>
  </div>
</nav>

<?php if (!empty($_SESSION['messages'])): ?>
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

---

### **7. Commit and Push Final Changes**
```bash
git add *
git commit -m "Project 00: config, PDO, contact form, flash messages"
git push
```

---

### **8. Submit the Project**
Submit the GitHub URL to your project folder:
```
https://github.com/[your-account]/[your-web3400-repo]/blob/main/projects/00/
```

---

## Production Notes
- Don’t commit real credentials.
- Disable detailed error display in production.
- Validate and sanitize all inputs.
- Escape user content on output (`htmlspecialchars`).

---

## Grading Checklist
- [ ] Project copied to `projects/00` and pushed.
- [ ] `config.php` loads, session + PDO configured.
- [ ] `.htaccess` present and denies `config.php` access.
- [ ] `contact_us` table created in database.
- [ ] `contact.php` validates input and uses prepared statements.
- [ ] Flash messages display and dismiss via BulmaJS.
- [ ] Templates wired correctly (`head.php`, `nav.php`, `footer.php`).

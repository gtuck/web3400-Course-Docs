# Project Configuration, Security, HTML Form Processing & Database Connections

This guide walks you through setting up a basic PHP web project with PDO for database interactions. You'll enhance a PHP template system that incorporates `head.php`, `nav.php`, and `footer.php`.

---

## **Setup Steps**

### **1. Copy Assignment 04 to the `projects/00` Folder**
1. Copy the folder recursively:
   ```bash
   cp -r assignments/04 projects/00
   ```
2. Commit and push changes:
   - **Stage Changes:** `git add *`
   - **Commit Changes:** `git commit -m "Created project 00 folder"`
   - **Push Changes:** `git push`

---

### **2. Create `config.php`**
- Serves as the core configuration file.
- Handles:
  - Site variables.
  - PDO database connection.
  - Session-based user messages.

#### Example `config.php`:
```php
<?php
// Site Variables
$siteName = "My PHP Site";
$contactEmail = "contact@example.com";
$contactPhone = "123-456-7890";

// Database Connection
try {
    $host = 'db';
    $dbname = 'web3400';
    $username = 'web3400';
    $password = 'password';
    $dsn = "mysql:host=$host;dbname=$dbname;charset=UTF8";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to database: " . $e->getMessage());
}

// Start Session
session_start();
if (!isset($_SESSION['messages'])) {
    $_SESSION['messages'] = [];
}
?>
```

---

### **3. Create `.htaccess`**
- Enables error display.
- Secures `config.php`.

#### Example `.htaccess`:
```apache
# Enable PHP display errors
php_flag display_errors on

# Secure config.php
<Files config.php>
    Order Deny,Allow
    Deny from all
</Files>
```

---

### **4. Create the Database Table**
1. Log in to **phpMyAdmin** using credentials:
   - **Username:** `web3400`
   - **Password:** `password`
2. Create the `contact_us` table:
   ```sql
   CREATE TABLE contact_us (
       id INT AUTO_INCREMENT PRIMARY KEY,
       name VARCHAR(255) NOT NULL,
       email VARCHAR(255) NOT NULL,
       message TEXT,
       submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

---

### **5. Create a Basic Contact Us Form**

#### File Structure:
- Create `contact.php` to include the form and logic for saving data.

#### Form HTML (`contact.php`):
```html
<section class="section">
    <h1 class="title">Contact Us</h1>
    <form class="box" action="contact.php" method="post">
        <div class="field">
            <label class="label">Your Name</label>
            <div class="control">
                <input class="input" type="text" name="name" placeholder="Bob Smith" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Your Email</label>
            <div class="control">
                <input class="input" type="email" name="email" placeholder="bsmith@email.com" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Your Message</label>
            <div class="control">
                <textarea class="textarea" name="message" required></textarea>
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

#### PHP Logic for Saving Data (Top of `contact.php`):
```php
<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare("INSERT INTO contact_us (name, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $message]);

    $_SESSION['messages'][] = 'Thank you for contacting us!';
    header('Location: contact.php');
    exit;
}
?>
```

---

### **6. Update `index.php`, `header.php`, `nav.php`, and `footer.php`**

#### `index.php`:
- Add this to the top of the file:
  ```php
  <?php include 'config.php'; ?>
  ```

#### `header.php`:
- Update the `<title>` tag:
  ```html
  <title><?= $siteName ?></title>
  ```

#### `footer.php`:
- Update the footer content:
  ```html
  <strong><?= $siteName ?></strong> | &copy; Copyright 2024 | The source code is licensed under MIT.
  ```

#### `nav.php`:
1. Update logo link: `href="index.php"`.
2. Replace site name with `<?= $siteName ?>`.
3. Update Contact Us button link: `href="contact.php"`.
4. Add a user message section:
   ```php
   <?php if (!empty($_SESSION['messages'])) : ?>
       <section class="notification is-warning">
           <button class="delete"></button>
           <?= implode('<br>', $_SESSION['messages']); ?>
           <?php $_SESSION['messages'] = []; ?>
       </section>
   <?php endif; ?>
   ```

---

### **7. Commit and Push Final Changes**
1. Stage Changes: `git add *`
2. Commit Changes: `git commit -m "Final update for project 00"`
3. Push Changes: `git push`
4. Confirm changes on GitHub.

---

### **8. Submit the Project**
Submit the URL to your updated project folder in this format:
```
https://github.com/[your-account-name]/[your-web3400-repo]/blob/main/projects/00/
```

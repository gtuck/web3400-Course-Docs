# PHP Syntax (Project 00)

This project will guide you through setting up a basic web project using PHP with PDO for database interaction. We will work within the PHP template system previously created that incorporates `head.php`, `nav.php`, and `footer.php`. All new code will be added between the `nav` and `footer` 'include' statements.

### Objective 1: Create `config.php`

`config.php` will serve as the core configuration file for our project. It will include the PDO database connection setup, site variables, and a user message system using a session array.

```php
<?php

// Site Variables
$siteName = "My PHP Site";
$contactEmail = "contact@example.com";
$contactPhone = "123-456-7890";

// Create the connection to your web3400 database
try {
    // Database connection variables
    $host = 'db';
    $dbname = 'web3400';
    $username = 'web3400';
    $password = 'password';
    $dsn = "mysql:host=$host;dbname=$dbname;charset=UTF8";

    // Create a PDO connection object
    $pdo = new PDO($dsn, $username, $password);

    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}

// Start a user session for the messages response system
session_start();

// Crate the message array and store it in a session variable
if (!isset($_SESSION['messages'])) {
    $_SESSION['messages'] = [];
}
// How to add a message to the array
// $_SESSION['messages'][] = "Message goes here";
?>
```

### Objective 2: Create `.htaccess`

The `.htaccess` file will enable `display_errors` for debugging purposes and will secure the `config.php` file from direct access.

```
# Enable PHP display errors
php_flag display_errors on

# Secure config.php
<Files config.php>
Order Deny,Allow
Deny from all
</Files>
```

### Objective 3: Overview of phpMyAdmin

1. **Log in to phpMyAdmin** using the username `web3400` and the password `password`.
2. **Create the `contact_us` table** with the following SQL command:

```sql
CREATE TABLE contact_us (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

3. **Explore the SQL tab features** in phpMyAdmin to understand how to execute SQL queries directly, import SQL files, and export data from your database.

#### Objective 4: Create a Basic Contact Us Form

1. **File Structure**: Create `contact.php`, containing our contact form and logic to save messages to the database.

2. **Form HTML (within `contact.php`)**:

```php
<?php include 'nav.php'; ?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Contact Us</h1>
    <form action="contact.php" method="post">
        <div class="field">
            <label class="label">Name</label>
            <div class="control">
                <input class="input" type="text" name="name" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="email" name="email" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Message</label>
            <div class="control">
                <textarea class="textarea" name="message" required></textarea>
            </div>
        </div>
        <div class="field">
            <div class="control">
                <button class="button is-link">Submit</button>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
<?php include 'footer.php'; ?>
```

3. **PHP to Save Form Data (at the top of `contact.php`)**:

```php
<?php
include 'config.php'; // Ensure this is at the top to use PDO for database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Prepare statement to avoid SQL injection
    $stmt = $pdo->prepare("INSERT INTO contact_us (name, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $message]);

    // Redirect or display a success message
    $_SESSION['message'] = '

Thank you for contacting us!';
    header('Location: contact.php');
    exit;
}
?>
```

This setup ensures a basic yet secure contact form that utilizes PHP, PDO, HTML5, and the Bulma CSS framework. By including FontAwesome, you can further enhance the visual appeal of your forms and navigation. Test your form thoroughly to ensure data is saved correctly to your `contact_us` table.

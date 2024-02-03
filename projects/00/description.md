# HTML forms & Connecting to a database (Project 00)

This project will guide you through setting up a basic web project using PHP with PDO for database interaction. We will work within the PHP template system previously created that incorporates `head.php`, `nav.php`, and `footer.php`. New HTML code will be added between the `<!-- BEGIN/END YOUR CONTENT -->` comments and PHP code will be added to the top of the page between the `<?php .... ?>` tags. When we need to inject PHP into the HTML it is often done with the shortcut `<?= $theValue ?>`.

## Copy Assignment 04 to the Project 00 folder.

1. Recursively copy your folder: Run `cp -r assignments/04 projects/00`.
2. Add, commit, and push the new project `00` folder to your repo.
   1. Stage the Change: Execute `git add *`.
   2. Commit the Change: Type `git commit -m "Created project 00 folder"`.
   3. Push the Change: Run `git push`.

## Create a `config.php` file

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

## Create a `.htaccess` file

The `.htaccess` file will enable `display_errors` for debugging purposes and to secure the `config.php` file from direct access.

```
# Enable PHP display errors
php_flag display_errors on

# Secure config.php
<Files config.php>
Order Deny, Allow
Deny from all
</Files>
```

## Create a database table and overview of phpMyAdmin

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

## Create a Basic Contact Us Form

1. **File Structure**: Create `contact.php`, containing our contact form and logic to save messages to the database.

2. **Form HTML (within `contact.php`)**:

```php
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Contact Us</h1>
    <form class="box" action="contact.php" method="post">
        <div class="field">
            <label class="label">Your Name</label>
            <div class="control has-icons-left">
                <span class="icon is-left">
                    <i class="fas fa-user"></i>
                </span>
                <input class="input" type="text" name="name" placeholder="Bob Smith" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Your Email</label>
            <div class="control has-icons-left">
                <span class="icon is-left">
                    <i class="fas fa-at"></i>
                </span>
                <input class="input" type="email" name="email" placeholder="bsmith@email.com" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Your message to us</label>
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
<section class="section">
    <h3 class="title">Or give us a call at</h3>
    <a class="button is-link" href="tel:<?= $contactPhone ?>">
        <span class="icon">
            <i class="fas fa-phone"></i>
        </span>
        <span><?= $contactPhone ?></span>
    </a>
</section>
<!-- END YOUR CONTENT -->
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
    $_SESSION['messages'][] = 'Thank you for contacting us!';
    header('Location: contact.php');
    exit;
}
?>
```

## Update `index.php`, `header.php`, `nav.php`, and `footer.php`

1. **index.php**: Add the `<?php include 'config.php'; ?>` statement to the top of the file
2. **header.php**: Update the `<title>` tag to `<title><?= $siteName ?></title>`
3. **footer.php**: Update the content of the `<p>` tag to `<strong><?= $siteName ?></strong> | &copy; Copyright 2024 | The source code is licensed under MIT.`
4. **nav.php**: There are multiple updates to the nav bar:
   1. Update the `href` for the site logo link to `href="index.php"`.
   2. Replace the site name placeholder with `<?= $siteName ?>`.
   3. Update the Contact Us button `href` to `href="contact.php"`.
   4. Add a user message section after the hero section:
   ```php
    <?php if (!empty($_SESSION['messages'])) : ?>
        <section class="notification is-warning">
            <button class="delete"></button>
            <?php echo implode('<br>', $_SESSION['messages']);
                  $_SESSION['messages'] = []; // Clear the user responses?>
        </section>
    <?php endif; ?>
   ```

## Conclusion

This setup ensures a basic yet secure contact form that utilizes PHP, PDO, HTML5, and the Bulma CSS framework. By including FontAwesome, you can further enhance the visual appeal of your forms and navigation. Test your form thoroughly to ensure data is saved correctly to your `contact_us` table.

## Stage, Commit, and Push the Final Changes
- **Objective**: Commit and push your completed project 00 changes in a VS Code Terminal.
- **Topics**:
  1. Stage the Change: Run `git add *`.
  2. Commit the Changes: Type `git commit -m "Final update for project 00"`.
  3. Push the Changes: Run `git push`.
  4. Confirm Changes on GitHub: Visit your forked repository on GitHub.

## Submitting the Project
- **Objective**: Submit the URL to your completed index.html file.
- **Topics**:
  1. Submit the URL of your updated project `00` folder in the format: `https://github.com/[your-account-name]/[your-web3400-repo]/blob/main/projects/00/`. Replace `[your-account-name]` with your GitHub username and `[your-web3400-repo]` with your repo name.

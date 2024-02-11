# User Account Creation (Project 01)

In this project, we will create systems for users to register an account, log in/out, and update the site navigation template. We'll follow the same structured approach, mirroring previous projects' design patterns and instructional style.

## Copy Project 00 to the Project 01 folder.

1. Recursively copy your folder: Run `cp -r projects/00 projects/01`.
2. Add, commit, and push the new project `01` folder to your repo.
   1. Stage the Change: Execute `git add *`.
   2. Commit the Change: Type `git commit -m "Created project 01"`.
   3. Push the Change: Run `git push`.

## Create the Users Table

1. **Log in to phpMyAdmin** using your credentials.
2. **Run the following SQL command** to create the user's table in your web3400 database:

```sql
CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    pass_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(255),
    sms BOOLEAN DEFAULT TRUE,
    subscribe BOOLEAN DEFAULT TRUE,
    role ENUM('admin', 'editor', 'user') DEFAULT 'user',
    created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
    activation_code VARCHAR(255),
    modified_on DATETIME ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME
);
```

This table includes fields needed to support user account management, including security features and account type.

## Create a `register.php` file in your Project 01 folder

1. **Form HTML**: Add the following HTML to your `register.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Create a user account</h1>
    <form class="box" action="register.php" method="post">
        <!-- Full Name -->
        <div class="field">
            <label class="label">Full Name</label>
            <div class="control">
                <input class="input" type="text" name="full_name" required>
            </div>
        </div>
        <!-- Email -->
        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="email" name="email" required>
            </div>
        </div>
        <!-- Password -->
        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input class="input" type="password" name="password" required>
            </div>
        </div>
        <!-- Phone -->
        <div class="field">
            <label class="label">Phone</label>
            <div class="control">
                <input class="input" type="tel" name="phone">
            </div>
        </div>
        <!-- sms -->
        <div class="field">
            <div class="control">
                <label class="checkbox">
                    <input name="sms" type="checkbox">
                    &nbsp;Yes, please send me text messages.
                </label>
            </div>
        </div>
        <!-- Subscribe -->
        <div class="field">
            <div class="control">
                <label class="checkbox">
                    <input name="subscribe" type="checkbox">
                    &nbsp;Yes, please add me to your mailing list.
                </label>
            </div>
        </div>
        <!-- Submit Button -->
        <div class="field is-grouped">
            <div class="control">
                <button type="submit" class="button is-link">Register</button>
            </div>
            <div class="control">
                <button type="reset" class="button is-link is-light">Reset</button>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
```

2. **PHP Processing**: Add the following PHP code to the top of your `register.php` file; this code will process the form data, check if the email is unique, insert data into the database, generate an activation link, and complete the account activation.

```php
<?php
include 'config.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract, sanitize user input, and assign data to variables
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt password
    $phone = htmlspecialchars($_POST['phone']);
    $sms = $_POST['sms'] == 'on' ? 1 : 0;
    $subscribe = $_POST['subscribe'] == 'on' ? 1 : 0;
    $activation_code = uniqid(); // Generate a unique id

    // Check if the email is unique
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `email` = ?");
    $stmt->execute([$email]);
    $userExists = $stmt->fetch();

    if ($userExists) {
        // Email already exists, prompt the user to choose another
        $_SESSION['messages'][] = "That email already exists. Please choose another or reset your passowrd";
        header('Location: register.php');
        exit;
    } else {
        // Email is unique, proceed with inserting the new user record
        $insertStmt = $pdo->prepare("INSERT INTO `users`(`full_name`, `email`, `pass_hash`, `phone`, `sms`, `subscribe`, `activation_code`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->execute([$full_name, $email, $password, $phone, $sms, $subscribe, $activation_code]);

        // Generate activation link. This is instead of sending a verification Email and or SMS message
        $activation_link = "?code=$activation_code";

        // Create an activation link message
        $_SESSION['messages'][] = "Welcome $full_name. To activate your account, <a href='$activation_link'>click here</a>.";
    }
}
// Check if an activation code is provided in the URL query string
if (isset($_GET['code'])) {
    $activationCode = $_GET['code'];

    try {
        // Prepare a SQL statement to select the user with the given activation code
        $stmt = $pdo->prepare("SELECT * FROM users WHERE activation_code = ? LIMIT 1");
        $stmt->execute([$activationCode]);
        $user = $stmt->fetch();

        // Check if user exists
        if ($user) {
            // User found. Now update the activated_on field with the current date and time
            $updateStmt = $pdo->prepare("UPDATE `users` SET `activation_code` = CONCAT('activated - ', NOW()) WHERE `id` = ?");
            $updateResult = $updateStmt->execute([$user['id']]);

            if ($updateResult) {
                // Update was successful
                $_SESSION['messages'][] = "Account activated successfully. You can now login.";
                header('Location: login.php');
                exit;
            } else {
                // Update failed
                $_SESSION['messages'][] = "Failed to activate account. Please try the activation link again or contact support.";
            }
        } else {
            // No user found with that activation code
            $_SESSION['messages'][] = "Invalid activation code. Please check the link or contact support.";
        }
    } catch (PDOException $e) {
        // Handle any database errors (optional)
        die("Database error occurred: " . $e->getMessage());
    }
}
?>
```

## Create a `login.php` file in your Project 01 folder

1. **Form HTML**: Add the following HTML to your `login.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Login</h1>
    <form class="box" action="login.php" method="post">
        <!-- Email -->
        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="email" name="email" required>
            </div>
        </div>
        <!-- Password -->
        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input class="input" type="password" name="password" required>
            </div>
        </div>
        <!-- Submit Button -->
        <div class="field">
            <div class="control">
                <button type="submit" class="button is-link">Login</button>
            </div>
        </div>
    </form>
    <a href="register.php" class="is-link"><strong>Create a new user account</strong></a>
</section>
<!-- END YOUR CONTENT -->
```

1. **PHP Processing**: We will code the PHP code for the `login.php` file together in class.

```php
<?php
include 'config.php';

//We will code this together in class

?>
```

## Create a `logout.php` file in your project 01 folder

1. **PHP Processing**: Add the following PHP to your `logout.php` file.

```php
<?php
    session_start();
    session_destroy();
    header('Location: login.php');
?>
```

## Update the `nav.php` template

1. **nav.php**: There are multiple updates to the nav bar:
   1. Replace the `Log in` button with:
      ```html
      <!-- BEGIN USER MENU -->
         <?php if (isset($_SESSION['loggedin'])) : ?>
            <div class="navbar-item has-dropdown is-hoverable">
               <a class="button navbar-link">
                  <span class="icon">
                     <i class="fas fa-user"></i>
                  </span>
               </a>
               <div class="navbar-dropdown">
                  <a class="navbar-item">Profile</a>
                  <hr class="navbar-divider">
                  <a href="logout.php" class="navbar-item">Logout</a>
               </div>
            </div>
         <?php else : ?>
            <a href="login.php" class="button is-link">Login</a>
         <?php endif; ?>
      <!-- END USER MENU -->
      ```

   3. Replace the `HERO` with the following code.
   ```php
    <?php if ($_SERVER['PHP_SELF'] == '/index.php') : ?>
        <!-- BEGIN HERO -->
        <section class="hero is-link">
            <div class="hero-body">
                <p class="title">
                    Hero title
                </p>
                <p class="subtitle">
                    Hero subtitle
                </p>
            </div>
        </section>
        <!-- END HERO -->
    <?php endif; ?>
   ```

## Conclusion
This setup not only enhances your application's security but also improves the user experience by providing clear feedback and easy-to-follow steps for account management and allows users to register a new account and login/out.

## Stage, Commit and Push the Final Changes
- **Objective**: Commit and push your completed project 01 changes in a VS Code Terminal.
- **Topics**:
  1. Stage the Change: Run `git add *`.
  2. Commit the Changes: Type `git commit -m "Final update for project 01"`.
  3. Push the Changes: Run `git push`.
  4. Confirm Changes on GitHub: Visit your forked repository on GitHub.

## Submitting the Project
- **Objective**: Submit the URL to your completed project 01 folder.
- **Topics**:
  1. Submit the URL of your updated project `01` folder in the format: `https://github.com/[your-account-name]/[your-web3400-repo]/blob/main/projects/01/`. Replace `[your-account-name]` with your GitHub username and `[your-web3400-repo]` with your repo name.

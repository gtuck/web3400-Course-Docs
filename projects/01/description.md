# User Account Creation (Project 01)

This tutorial will guide you through creating a user account creation feature for a web application. We'll follow a structured approach, mirroring the design patterns and instructional style from Project 00.

## Copy Project 00 to the Project 01 folder.

1. Recursively copy your folder: Run `cp -r projects/00 projects/01`.
2. Add, commit, and push the new project `01` folder to your repo.
   1. Stage the Change: Execute `git add *`.
   2. Commit the Change: Type `git commit -m "Created project 01"`.
   3. Push the Change: Run `git push`.

## Create the User Table

1. **Log in to phpMyAdmin** using your credentials.
2. **Run the following SQL command** to create the user's table in your web3400 database:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL UNIQUE,
    pass_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(255),
    role ENUM('admin', 'editor', 'user') DEFAULT 'user',
    created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
    activation_code VARCHAR(255),
    modified_on DATETIME ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME
);
```

This table includes fields needed to support user account management, including security features and account status tracking.

## Create a `register.php` file in your project 01 folder

1. **Form HTML**: Add the following HTML to your `register.php` file.

```php
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
        <!-- Username -->
        <div class="field">
            <label class="label">Username</label>
            <div class="control">
                <input class="input" type="text" name="username" required>
            </div>
        </div>
        <!-- Password -->
        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input class="input" type="password" name="password" required>
            </div>
        </div>
        <!-- Email -->
        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="email" name="email" required>
            </div>
        </div>
        <!-- Phone -->
        <div class="field">
            <label class="label">Phone</label>
            <div class="control">
                <input class="input" type="tel" name="phone">
            </div>
        </div>
        <!-- Submit Button -->
        <div class="field">
            <div class="control">
                <button type="submit" class="button is-primary">Register</button>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
```

2. **PHP Processing**: At the following PHP code to the top of your `register.php` file, this code will process the form data, check if the username is unique, insert data into the database, and generate an activation link and complete the account activation.

```php
<?php
include 'config.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract, sanitize user input, and assign data to variables
    $full_name = htmlspecialchars($_POST['full_name']);
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt password
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $activation_code = uniqid(); // Generate a unique id

    // Check if the username is unique
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $userExists = $stmt->fetch();

    if ($userExists) {
        // Username already exists, prompt the user to choose another username
        $_SESSION['messages'][] = "Username already exists. Please choose another username.";
    } else {
        // Username is unique, proceed with inserting the new user record
        $insertStmt = $pdo->prepare("INSERT INTO users (full_name, username, pass_hash, email, phone, activation_code) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStmt->execute([$full_name, $username, $password, $email, $phone, $activation_code]);
        
        // Generate activation link (pseudo code)
        $activation_link = "?code=$activation_code";
        
        // Create activation link message
        $_SESSION['messages'][] = "Welcome $username. To activate your account, <a href='$activation_link'>click here</a>.";
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
            // User found, now update the activated_on field with the current date and time
            $updateStmt = $pdo->prepare("UPDATE users SET activation_code = NOW() WHERE id = ?");
            $updateResult = $updateStmt->execute([$user['id']]);

            if ($updateResult) {
                // Update was successful
                $_SESSION['messages'][] = "Account activated successfully. You can now login.";
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
} else {
    // No activation code provided
    $_SESSION['messages'][] = "No activation code provided. Please check your activation link.";
}
?>
```

## Conclusion

Remember to validate all user input and handle errors gracefully. This setup not only enhances your application's security but also improves the user experience by providing clear feedback and easy-to-follow steps for account management.

## Stage, Commit, and Push the Final Changes
- **Objective**: Commit and push your completed project 01 changes in a VS Code Terminal.
- **Topics**:
  1. Stage the Change: Run `git add *`.
  2. Commit the Changes: Type `git commit -m "Final update for project 01"`.
  3. Push the Changes: Run `git push`.
  4. Confirm Changes on GitHub: Visit your forked repository on GitHub.

## Submitting the Project
- **Objective**: Submit the URL to your completed index.html file.
- **Topics**:
  1. Submit the URL of your updated project `01` folder in the format: `https://github.com/[your-account-name]/[your-web3400-repo]/blob/main/projects/01/`. Replace `[your-account-name]` with your GitHub username and `[your-web3400-repo]` with your repo name.

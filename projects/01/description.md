# User Account Creation & Password Reset (Project 01)

This tutorial will guide you through creating a user account creation and password reset feature for a web application. We'll follow a structured approach, mirroring the design patterns and instructional style from Project 00.

## Copy Project 00 to the Project 01 folder.

1. Recursively copy your folder: Run `cp -r projects/00 projects/01`.
2. Add, commit, and push the new project `01` folder to your repo.
   1. Stage the Change: Execute `git add *`.
   2. Commit the Change: Type `git commit -m "Created project 01"`.
   3. Push the Change: Run `git push`.

## Learning Objective 1: Create the User Table

1. **Log in to phpMyAdmin** using your credentials.
2. **Run the following SQL command** to create the user's table:

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
    activated_on DATETIME,
    activation_code VARCHAR(255),
    modified_on DATETIME ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME,
    security_question VARCHAR(255),
    security_question_answer VARCHAR(255)
);
```

This table includes all necessary fields to support user account management, including security features and account status tracking.

## Learning Objective 2: Create a `register.php` File

1. **Form HTML**: Create `register.php` to include a registration form.

```php
<?php include 'nav.php'; ?>
<section class="section">
    <div class="container">
        <h1 class="title">Register</h1>
        <form action="register.php" method="post">
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
            <!-- Security Question -->
            <div class="field">
                <label class="label">Security Question</label>
                <div class="control">
                    <div class="select">
                        <select name="security_question">
                            <option value="Your first pet's name?">Your first pet's name?</option>
                            <option value="The city you were born in?">The city you were born in?</option>
                            <!-- Add more questions as needed -->
                        </select>
                    </div>
                </div>
            </div>
            <!-- Security Question Answer -->
            <div class="field">
                <label class="label">Answer</label>
                <div class="control">
                    <input class="input" type="text" name="security_question_answer" required>
                </div>
            </div>
            <!-- Submit Button -->
            <div class="field">
                <div class="control">
                    <button type="submit" class="button is-primary">Register</button>
                </div>
            </div>
        </form>
    </div>
</section>
<?php include 'footer.php'; ?>
```

2. **PHP Processing**: At the top of `register.php`, add PHP logic to process the form data, insert it into the database, and generate an activation link.

```php
<?php
include 'config.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract and sanitize user input
    $full_name = htmlspecialchars($_POST['full_name']);
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt password
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $activation_code = uniqid();
    $security_question = htmlspecialchars($_POST['security_question']);
    $security_question_answer = htmlspecialchars($_POST['security_question_answer']);
    
    // Prepare the SQL statement to prevent SQL injection
    $stmt = $pdo->prepare("INSERT INTO users (full_name, username, pass_hash, email, phone, activation_code, security_question, security_question_answer) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Execute the statement with the user data
    $stmt->execute([$full_name, $username, $password, $email, $phone, $activation_code, $security_question, $security_question_answer]);
    
    // Generate activation link (pseudo code)
    $activation_link = "http://yourdomain.com/activate_acct.php?code=$activation_code";
    
    echo "Welcome $username. To activate your account, <a href='$activation_link'>click here</a>.";
}
?>
```

## Learning Objective 3: Password Reset Link

1. **Create `reset-pwd.php`**: This file will handle password reset requests.

```php
<?php include 'nav.php'; ?>
<!-- Password Reset Form -->
<section class="section">
    <div class="container">
        <h1 class="title">Reset Password</h1>
        <!-- Form asking for username, email, and security question -->
        <!-- After validation, show new password form -->
    </div>
</section>
<?php include 'footer.php'; ?>
```

2. **PHP Logic**: Add logic to validate user input against the database and allow the user to enter a new password.

### Learning Objective 4: Account Activation

1. **Create `activate_acct.php`**: This page activates or reactivates user accounts based on the provided activation code.

```php
<?php
include 'config.php'; // Include database connection

// Check if the activation code is in the URL
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    
    // Validate the code and activate the account
    // Update the `activated_on` field in the users table for the user with the matching activation code
}

// Provide feedback to the user about the status of the activation
?>
```

## Conclusion

Remember to validate all user input and handle errors gracefully. This setup not only enhances your application's security but also improves the user experience by providing clear feedback and easy-to-follow steps for account management.

## Stage, Commit, and Push the Final Changes
- **Objective**: Commit and push your completed project 00 changes in a VS Code Terminal.
- **Topics**:
  1. Stage the Change: Run `git add *`.
  2. Commit the Changes: Type `git commit -m "Final update for project 01"`.
  3. Push the Changes: Run `git push`.
  4. Confirm Changes on GitHub: Visit your forked repository on GitHub.

## Submitting the Project
- **Objective**: Submit the URL to your completed index.html file.
- **Topics**:
  1. Submit the URL of your updated project `01` folder in the format: `https://github.com/[your-account-name]/[your-web3400-repo]/blob/main/projects/01/`. Replace `[your-account-name]` with your GitHub username and `[your-web3400-repo]` with your repo name.

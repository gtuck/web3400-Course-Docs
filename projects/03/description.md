# User Management System (Project 03)

In this project, you will develop a user management system that allows an admin user to add, modify, and delete user accounts, including managing each user's role. You'll follow a structured approach, similar to previous projects, to ensure proper account management functionalities and user experience. The HTML part of each page is complete and provided for you. You must complete each step described in the PHP Comments. Hint: Many code snippets from previous projects may be useful as starter code for this project.

## Copy Project 02 to the Project 03 folder

- Recursively copy the project folder.
- Stage, commit, and push your new project to GitHub.

## Create a `admin_dashboard.php` File in Your Project 03 Folder

1. **HTML Structure**: Add the following HTML structure to your `admin_dashboard.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Admin Dashboard</h1>
    <p>Admin dashboard content will be created in a future project...</p>
</section>
<!-- END YOUR CONTENT -->
```

1. **PHP Processing**: Complete the following coding steps by adding your code to the top of the `admin_dashboard.php` file. Your finished file will secure the page so only admin users can access it.

```php
<?php
// Step 1: Include config.php file

// Step 2: Secure and only allow 'admin' users to access this page
?>
```

## Create a `users_manage.php` File in Your Project 03 Folder

1. **HTML Structure**: Add the following HTML structure to your `users_manage.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Manage Users</h1>
    <!-- Add User Button -->
    <div class="buttons">
        <a href="user_add.php" class="button is-link">Add User</a>
    </div>
    <!-- User Table -->
    <table class="table is-fullwidth">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Fetch Users from Database and Populate Table Rows Dynamically -->
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= $user['full_name'] ?></td>
                    <td><?= $user['email'] ?></td>
                    <td><?= $user['role'] ?></td>
                    <td>
                        <!-- Edit User Link -->
                        <a href="user_edit.php?id=<?= $user['id'] ?>" class="button is-info">
                            <i class="fas fa-edit"></i>
                        </a>
                        <!-- Delete User Form -->
                        <a href="user_delete.php?id=<?= $user['id'] ?>" class="button is-danger">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<!-- END YOUR CONTENT -->
```

1. **PHP Processing**: Complete the following coding steps by adding your code to the top of the `users_manage.php` file. Your finished file will fetch users from the database and populate the user table dynamically.

```php
<?php
// Step 1: Include config.php file

// Step 2: Secure and only allow 'admin' users to access this page

// Step 3: Prepare the SQL query template to select all users from the database
// ex. $stmt = $pdo->prepare('SQL GOES HERE...');

// Step 4: Execute the query
// ex. $stmt->execute();

// Step 5: Fetch and store the results in the $users associative array
// ex. $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Step 6: Check if the query returned any rows. If not, display the message: "There are no user records in the database."
// ex. if (!$users) {...}
?>
```

## Create an `user_add.php` File in Your Project 03 Folder

1. **HTML Form**: Add the following HTML form to your `user_add.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Add User</h1>
    <form action="user_add.php" method="post">
        <div class="field">
            <label class="label">Full Name</label>
            <div class="control">
                <input class="input" type="text" name="full_name" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="email" name="email" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input class="input" type="password" name="password" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Role</label>
            <div class="control">
                <div class="select">
                    <select name="role">
                        <option value="admin">Admin</option>
                        <option value="editor">Editor</option>
                        <option value="user" selected>User</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="field">
            <label class="label">User Bio</label>
            <div class="control">
                <textarea class="textarea" name="user_bio"></textarea>
            </div>
        </div>
        <div class="field is-grouped">
            <div class="control">
                <button type="submit" class="button is-link">Add User</button>
            </div>
            <div class="control">
                <a href="users_manage.php" class="button is-link is-light">Cancel</a>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
```

2. **PHP Processing**: Implement PHP code to process the form submission and insert a new user record into the database.

```php
<?php
// Step 1: Include config.php file

// Step 2: Secure and only allow 'admin' users to access this page

// Step 3: You can use the register.php page for most of this code. However, you will need to add a handler for the user role field.
?>
```

## Create an `user_edit.php` File in Your Project 03 Folder

1. **HTML Form**: Add the following HTML form to your `user_edit.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Edit User</h1>
    <form action="user_edit.php" method="post">
        <!-- Populate Form Fields with User Data -->
        <input type="hidden" name="id" value="<?= $user['id'] ?>">
        <div class="field">
            <label class="label">Full Name</label>
            <div class="control">
                <input class="input" type="text" name="full_name" value="<?= $user['full_name'] ?>" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="email" name="email" value="<?= $user['email'] ?>" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Role</label>
            <div class="control">
                <div class="select">
                    <select name="role">
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="editor" <?= $user['role'] === 'editor' ? 'selected' : '' ?>>Editor</option>
                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="field">
            <label class="label">User Bio</label>
            <div class="control">
                <textarea class="textarea" name="user_bio"><?= $user['user_bio'] ?></textarea>
            </div>
        </div>
        <div class="field is-grouped">
            <div class="control">
                <button type="submit" class="button is-link">Update User</button>
            </div>
            <div class="control">
                <a href="users_manage.php" class="button is-link is-light">Cancel</a>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
```

2. **PHP Processing**: Implement PHP code to retrieve user data from the database and update the user record based on the form submission.

```php
<?php
// Step 1: Include config.php file

// Step 2: Secure and only allow 'admin' users to access this page

// Step 3: Check if the update form was submitted. If so, update user details. Similar steps as in user_add.php but with an UPDATE SQL query

// Step 4: Else it's an initial page request, fetch the user's current data from the database by preparing and executing a SQL statement that uses the user gets the user id from the query string (ex. $_GET['id'])

?>
```

## Create a `user_delete.php` File in Your Project 03 Folder
1. **HTML Form**: Add the following HTML form to your `user_edit.php` file.

```html
<section class="section">
    <h1 class="title">User Delete</h1>
    <p class="subtitle">Are you sure you want to delete the user: <?= $user['full_name']?></p>
    <div class="buttons">
        <a href="?id=<?= $user['id'] ?>&confirm=yes" class="button is-success">Yes</a>
        <a href="?id=<?= $user['id'] ?>&confirm=no" class="button is-danger">No</a>
    </div>
</section>
```
1. **PHP Processing**: Add PHP code to delete a user record from the database when the delete form is submitted.

```php
<?php
// Step 1: Include config.php file

// Step 2: Secure and only allow 'admin' users to access this page

// Step 3: Check if the $_GET['id'] exists; if it does, get the user the record from the database and store it in the associative array $user. If a user record with that ID does not exist, then display the message "A user with that ID did not exist."

// Step 4: Check if $_GET['confirm'] == 'yes'. This means they clicked the 'yes' button to confirm the removal of the record. Prepare and execute a SQL DELETE statement where the user id == the $_GET['id']. Else (meaning they clicked 'no'), return them to the users_manage.php page.

?>
```

## Update the Navigation Template (`nav.php`)

1. **nav.php**: Update the navigation menu to include the `Admin` menu and the link for managing users. The code should be added to the `navbar-start` section of the main navbar after the `home` and `about` links.
   
```php
<!-- BEGIN ADMIN MENU -->
<?php if (isset($_SESSION['loggedin']) && $_SESSION['user_role'] == 'admin') : ?>
   <div class="navbar-item has-dropdown is-hoverable">
      <a class="navbar-link">
         Admin
      </a>
      <div class="navbar-dropdown">
         <a href="users_manage.php" class="navbar-item">
            Manage Users
         </a>
      </div>
   </div>
<?php endif; ?>
<!-- END ADMIN MENU -->
```

## Update the `profile.php` page

1. **profile.php**: Update the page by adding a `tag` displacing the user's role (i.e., admin, editor, user) from the database.
   
```php
...
<p class="title"><?= $user['full_name'] ?> | <span class="tag is-info is-medium"><?= $user['role'] ?></span></p>
...
```

## Final Steps

- Test your application thoroughly to catch and fix any bugs or issues.
- Ensure all files are correctly added and committed to your repository before pushing.
- Stage, commit, and push your final changes to GitHub.
- Submit your project URL as previously instructed, ensuring your GitHub repository is up to date so it can be accessed and evaluated.

## Conclusion

This user management system provides essential functionalities for an admin user to manage user accounts efficiently. Following these steps, you can add, modify, and delete user accounts, including collecting each user's role, effectively enhancing your web application's functionality and user management capabilities.

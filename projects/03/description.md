# User Management System (Project 03)

In this project, we will develop a user management system that allows an admin user to add, modify, and delete user accounts, including managing each user's role. We'll follow a structured approach, similar to previous projects, to ensure proper account management functionalities and user experience.

## Copy Project 02 to the Project 03 folder

1. Recursively copy your folder: Run `cp -r projects/02 projects/03`.
2. Add, commit, and push the new project `03` folder to your repository.
   1. Stage the Change: Execute `git add *`.
   2. Commit the Change: Type `git commit -m "Created project 03"`.
   3. Push the Change: Run `git push`.

## Enhance the Users Table

1. **Update the Database Schema**: Add a new field `user_bio` to the `users` table to store user biographies.
   
   ```sql
   ALTER TABLE users
   ADD COLUMN user_bio TEXT;
   ```

This modification allows us to store additional information about users in the database.

## Create a `manage_users.php` File in Your Project 03 Folder

1. **HTML Structure**: Add the following HTML structure to your `manage_users.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Manage Users</h1>
    <!-- Add User Button -->
    <div class="buttons">
        <a href="add_user.php" class="button is-link">Add User</a>
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
                        <a href="edit_user.php?id=<?= $user['id'] ?>" class="button is-small is-link">Edit</a>
                        <!-- Delete User Form -->
                        <form action="delete_user.php" method="post" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <button type="submit" class="button is-small is-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<!-- END YOUR CONTENT -->
```

2. **PHP Processing**: Code the PHP to fetch users from the database and populate the user table dynamically.

## Create an `add_user.php` File in Your Project 03 Folder

1. **HTML Form**: Add the following HTML form to your `add_user.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Add User</h1>
    <form action="process_add_user.php" method="post">
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
                <a href="manage_users.php" class="button is-link is-light">Cancel</a>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
```

2. **PHP Processing**: Implement PHP code to process the form submission and insert a new user record into the database.

## Create an `edit_user.php` File in Your Project 03 Folder

1. **HTML Form**: Add the following HTML form to your `edit_user.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Edit User</h1>
    <form action="process_edit_user.php" method="post">
        <!-- Populate Form Fields with User Data -->
        <input type="hidden" name="id" value="<?= $user['id'] ?>">
        <div class="field">
            <label class="label">Full Name</label>
            <div class="control">
                <input class="input" type="text" name="full

_name" value="<?= $user['full_name'] ?>" required>
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
                <a href="manage_users.php" class="button is-link is-light">Cancel</a>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
```

2. **PHP Processing**: Implement PHP code to retrieve user data from the database and update the user record based on the form submission.

## Create a `delete_user.php` File in Your Project 03 Folder

1. **PHP Processing**: Add PHP code to delete a user record from the database when the delete form is submitted.

## Update the Navigation Template (`nav.php`)

1. **nav.php**: Update the navigation menu to include links for managing users.
   
```php
<!-- BEGIN NAVIGATION -->
<div class="navbar-start">
    <a href="index.php" class="navbar-item">Home</a>
    <!-- Add User Management Links -->
    <?php if ($_SESSION['role'] === 'admin') : ?>
        <a href="manage_users.php" class="navbar-item">Manage Users</a>
    <?php endif; ?>
</div>
<div class="navbar-end">
    <!-- User Authentication Links -->
    <?php if (isset($_SESSION['loggedin'])) : ?>
        <div class="navbar-item has-dropdown is-hoverable">
            <a class="navbar-link">
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
        <a href="login.php" class="navbar-item">Login</a>
    <?php endif; ?>
</div>
<!-- END NAVIGATION -->
```

## Conclusion

This user management system provides essential functionalities for an admin user to manage user accounts efficiently. By following these steps, you'll be able to add, modify, and delete user accounts, including managing each user's role, effectively enhancing your web application's functionality and user management capabilities.

## Stage, Commit and Push the Final Changes

- **Objective**: Commit and push your completed project 03 changes in a VS Code Terminal.
- **Topics**:
  1. Stage the Change: Run `git add *`.
  2. Commit the Changes: Type `git commit -m "Final update for project 03"`.
  3. Push the Changes: Run `git push`.
  4. Confirm Changes on GitHub: Visit your forked repository on GitHub.

## Submitting the Project

- **Objective**: Submit the URL to your completed project 03 folder.
- **Topics**:
  1. Submit the URL of your updated project `03` folder in the format: `https://github.com/[your-account-name]/[your-web3400-repo]/blob/main/projects/03/`. Replace `[your-account-name]` with your GitHub username and `[your-web3400-repo]` with your repo name.
# Create and update user profile page (Project 02)

In this project, we will expand our user management system to include a user profile page where users can view and update their profile details. We will add a new field `user_bio` to the `users` table to store additional information about the user. We will update the `register.php` page to included the new `user_bio` field.

## Copy Project 01 to the Project 02 folder

1. Recursively copy your folder: Run `cp -r projects/01 projects/02`.
2. Add, commit, and push the new project `02` folder to your repository.
   1. Stage the Change: Execute `git add *`.
   2. Commit the Change: Type `git commit -m "Created project 02"`.
   3. Push the Change: Run `git push`.

## Update the Users Table

1. **Log in to phpMyAdmin** using your credentials.
2. **Run the following SQL command** to add a new field `user_bio` to the `users` table:

```sql
ALTER TABLE users ADD COLUMN user_bio TEXT;
```

This field will store additional information about the user's profile.

## Create a `profile.php` file in your Project 02 folder

1. **Profile Page HTML**: Create a new file named `profile.php` and add the following HTML to it.

```php
<?php
include 'config.php';

/*
  Get a Gravatar URL for a specified email address or a Placeholder image one is not available
  the source code is at https://gravatar.com/site/implement/images/php/
*/
function get_gravatar($email, $s = 128, $d = 'mp', $r = 'g', $img = false, $atts = array())
{
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email)));
    $url .= "?s=$s&d=$d&r=$r";
    if ($img) {
        $url = '<img src="' . $url . '"';
        foreach ($atts as $key => $val)
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}

try {
    // Get user info from the database
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `id` = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    // Handle any database errors (optional)
    die("Database error occurred: " . $e->getMessage());
}

?>
<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Profile</h1>
    <div class="card">
        <div class="card-content">
            <div class="media">
                <div class="media-left">
                    <figure class="image is-128x128">
                        <img class="is-rounded" src="<?= get_gravatar($user['email']) ?>" alt="<?= $user['full_name'] ?> profile image">
                    </figure>
                </div>
                <div class="media-content">
                    <p class="title"><?= $user['full_name'] ?></p>
                    <p class="subtitle"><?= $user['email'] ?></p>
                    <p class="subtitle"><?= $user['phone'] ?></p>
                </div>
            </div>

            <div class="content">
                <?= $user['user_bio'] ?>
                Last login: <time datetime="2016-1-1"><?= $user['last_login'] ?></time>
            </div>
        </div>
        <footer class="card-footer">
            <a href="profile_update.php" class="card-footer-item">Edit</a>
        </footer>
    </div>
</section>
<!-- END YOUR CONTENT -->

<?php include 'templates/footer.php'; ?>
```

1. **Profile Page PHP**: This PHP code retrieves user information from the database and displays it on the profile page. It also includes a function to generate a Gravatar image based on the user's email address.

## Create a `profile_update.php` file in your Project 02 folder

1. **Profile Update Page HTML**: Create a new file named `profile_update.php` and add the following HTML to it.

```php
<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Retrieve form data
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $user_bio = $_POST['user_bio'];

        // Update user record in the database
        $stmt = $pdo->prepare("UPDATE `users` SET `full_name` = ?, `email` = ?, `phone` = ?, `user_bio` = ? WHERE `id` = ?");
        $stmt->execute([$full_name, $email, $phone, $user_bio, $_SESSION['user_id']]);

        // Redirect user to profile page after successful update
        header('Location: profile.php');
        exit;
    } catch (PDOException $e) {
        // Handle any database errors (optional)
        die("Database error occurred: " . $e->getMessage());
    }
}

try {
    // Get user info from the database
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `id` = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    // Handle any database errors (optional)
    die("Database error occurred: " . $e->getMessage());
}

?>
<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Update Profile</h1>
    <form class="box" action="profile_update.php" method="post">
        <!-- Full Name -->
        <div class="field">
            <label class="label">Full Name</label>
            <div class="control">
                <input class="input" type="text" name="full_name" value="<?= $user['full_name'] ?>" required>
            </div>
        </div>
        <!-- Email -->
        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="email" name="email" value="<?= $user['email'] ?>" required>
            </div>
        </div>
        <!-- Phone -->
        <div class="field">
            <label class="label">Phone</label>
            <div class="control">
                <input class="input" type="tel" name="phone" value="<?= $user['phone'] ?>">
            </div>
        </div>
        <!-- Bio -->
        <div class="field">
            <label class="label">Bio</label>
            <div class="control">
                <textarea class="textarea" name="user_bio"><?= $user['user_bio'] ?></textarea>
            </div>
        </div>
        <!-- Submit Button -->
        <div class="field">
            <div class="control">
                <button type="submit" class="button is-link">Update Profile</button>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->

<?php include 'templates/footer.php'; ?>
```

2. **Profile Update Page PHP**: This page allows users to update their profile information. It pre-populates the form fields with the user's existing information fetched from the database.

## Conclusion
With the creation of the profile and profile update pages, users can now manage their profile details easily. By adding the `user_bio` field to the `users` table, we've expanded the functionality of our user management system, providing users with a more personalized experience.

## Stage, Commit and Push the Final Changes
- **Objective**: Commit and push your completed project 02 changes

 to your repository.
- **Steps**:
  1. Stage the changes: `git add *`.
  2. Commit the changes: `git commit -m "Added profile and profile update pages"`.
  3. Push the changes: `git push`.

Now you have successfully implemented user profile management functionality in your web application.
# Simple Blog System (Project 04)

In this project, you will develop a simple blog system that allows users to create, read, update, and delete blog posts. The system will also include user authentication, allowing only logged-in users to create and manage their posts. You'll follow a structured approach to ensure the blog system functions correctly and provides a good user experience.

## Copy Project 03 to the Project 04 folder

- Recursively copy the project folder.
- Stage, commit, and push your new project to GitHub.

## Set Up the Database

Before coding, you must set up the database table to store the blog posts. Use the following SQL statement to create the `blog_posts` table:

```sql
CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `comments_count` mediumint(3) NOT NULL DEFAULT 0,
  `likes_count` mediumint(3) NOT NULL DEFAULT 0,
  `favs_count` mediumint(3) NOT NULL DEFAULT 0,
  `modified_on` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
```

This table includes columns for the post ID, title, content, author, and creation timestamp. Adjust the table structure as needed for your application. After creating the table, you can proceed with the rest of the project steps as outlined.

## Update the `config.php` file

**PHP Processing**: Open your `config.php` file and add the following function at the end of the file:

```php
function time_ago($datetime) {
    $time_ago = strtotime($datetime);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;

    $minutes      = round($seconds / 60);           // value 60 is seconds
    $hours        = round($seconds / 3600);         // value 3600 is 60 minutes * 60 sec
    $days         = round($seconds / 86400);        // value 86400 is 24 hours * 60 minutes * 60 sec
    $weeks        = round($seconds / 604800);       // value 604800 is 7 days * 24 hours * 60 minutes * 60 sec
    $months       = round($seconds / 2629440);      // value 2629440 is ((365+365+365+365+366)/5/12) days * 24 hours * 60 minutes * 60 sec
    $years        = round($seconds / 31553280);     // value 31553280 is ((365+365+365+365+366)/5) days * 24 hours * 60 minutes * 60 sec

    if ($seconds <= 60) {
        return "Just now";
    } else if ($minutes <= 60) {
        return $minutes == 1 ? "one minute ago" : "$minutes minutes ago";
    } else if ($hours <= 24) {
        return $hours == 1 ? "an hour ago" : "$hours hours ago";
    } else if ($days <= 7) {
        return $days == 1 ? "yesterday" : "$days days ago";
    } else if ($weeks <= 4.3) { // 4.3 == 30/7
        return $weeks == 1 ? "a week ago" : "$weeks weeks ago";
    } else if ($months <= 12) {
        return $months == 1 ? "a month ago" : "$months months ago";
    } else {
        return $years == 1 ? "one year ago" : "$years years ago";
    }
}
```

This function calculates the time elapsed since a given datetime and returns a human-readable string such as "2 hours ago" or "yesterday".

### Usage

You can now use the `time_ago` function in any PHP file that includes `config.php`. For example, you can display the publication date of a blog post like this:

```php
echo time_ago($post['created_at']);
```

## Create the `blog_post.php` file

**HTML Structure**: Add the following HTML structure to your `blog_post.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title"><?= $post['title'] ?></h1>
    <div class="box">
        <article class="media">
            <div class="media-content">
                <div class="content">
                    <p>
                    <?= mb_substr($post['content'], 0, 200) . (mb_strlen($post['content']) > 200 ? "<a href=blog_post.php?id={$post['id']}> read more...</a>" : "") ?>
                    </p>
                    <p>
                        <small><strong>Author: <?= $post['author'] ?></strong>
                            | Published: <?= time_ago($post['created_at']) ?>
                            <?php if ($post['modified_on'] !== NULL) : ?>
                                | Updated: <?= time_ago($post['modified_on']) ?>
                            <?php endif; ?>
                        </small>
                    </p>
                </div>
                <p class="buttons">
                    <a class="button is-small is-rounded">
                        <span class="icon is-small">
                            <i class="fas fa-thumbs-up"></i>
                        </span>
                        <span><?= $post['likes_count'] ?></span>
                    </a>
                    <a class="button is-small is-rounded">
                        <span class="icon is-small">
                            <i class="fas fa-star"></i>
                        </span>
                        <span><?= $post['favs_count'] ?></span>
                    </a>
                    <a class="button is-small is-rounded">
                        <span class="icon is-small">
                            <i class="fas fa-comment"></i>
                        </span>
                        <span><?= $post['comments_count'] ?></span>
                    </a>
                </p>
            </div>
        </article>
    </div>
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Complete the following coding steps by adding your code to the top of the `blog_posts.php` file. Your finished file will fetch posts from the database and populate the posts table dynamically.

```php
<?php
// Step 1: Include config.php file

// Step 2: Check if the $_GET['id'] exists; if it does, get the blog post record from the database and store it in the associative array $post. If a user record with that ID does not exist, display the message "A blog post with that ID did not exist."

// Step 3: Check if the post exists
?>
```

## Create the `blog_posts.php` file

**HTML Structure**: Add the following HTML structure to your `blog_posts.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Blog Posts</h1>
    <!-- Add Post Button -->
    <div class="buttons">
        <a href="blog_post_add.php" class="button is-link">Add Post</a>
    </div>
    <!-- Posts Table -->
    <table class="table is-bordered is-striped is-hoverable is-fullwidth">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Content</th>
                <th>Author</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Fetch Posts from Database and Populate Table Rows Dynamically -->
            <?php foreach ($posts as $post) : ?>
                <tr>
                    <td><?= $post['id'] ?></td>
                    <td><?= mb_substr($post['title'], 0, 30) . (mb_strlen($post['title']) > 30 ? '...' : '') ?></td>
                    <td><?= mb_substr($post['content'], 0, 50) . (mb_strlen($post['content']) > 50 ? '...' : '') ?></td>
                    <td><?= $post['author'] ?></td>
                    <td>
                        <!-- Edit Post Link -->
                        <a href="blog_post_edit.php?id=<?= $post['id'] ?>" class="button is-info">
                            <i class="fas fa-edit"></i>
                        </a>
                        <!-- Delete Post Form -->
                        <a href="blog_post_delete.php?id=<?= $post['id'] ?>" class="button is-danger">
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

**Note:** In this code, the `mb_substr` function is used to extract the first 30 characters of the title and the first 50 characters of the content. The ternary operator `(mb_strlen($post['title']) > 30 ? '...' : '')` is used to append an ellipsis (`...`) if the original string is longer than the specified length, indicating that the text has been truncated.

**PHP Processing**: Complete the following coding steps by adding your code to the top of the `blog_posts.php` file. Your finished file will fetch posts from the database and populate the posts table dynamically.

```php
<?php
// Step 1: Include config.php file

// Step 2: Secure and only allow logged-in users to access this page

// Step 3: Prepare the SQL query template to select all posts from the database
// ex. $stmt = $pdo->prepare('SELECT blog_posts.*, users.full_name AS author FROM blog_posts JOIN users ON blog_posts.user_id = users.id ORDER BY blog_posts.created_at DESC');

// Step 4: Execute the query
// ex. $stmt->execute();

// Step 5: Fetch and store the results in the $posts associative array
// ex. $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Step 6: Check if the query returned any rows. If not, display the message: "There are no blog posts in the database."
// ex. if (!$posts) {...}
?>
```

## Create the `blog_post_add.php` file

**HTML Form**: Add the following HTML form to your `blog_post_add.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Add Post</h1>
    <form action="blog_post_add.php" method="post">
        <!-- Title -->
        <div class="field">
            <label class="label">Title</label>
            <div class="control">
                <input class="input" type="text" name="title" required>
            </div>
        </div>
        <!-- Content -->
        <div class="field">
            <label class="label">Content</label>
            <div class="control">
                <textarea class="textarea" id="content" name="content" required></textarea>
            </div>
        </div>
        <!-- Submit -->
        <div class="field is-grouped">
            <div class="control">
                <button type="submit" class="button is-link">Add Post</button>
            </div>
            <div class="control">
                <a href="blog_posts.php" class="button is-link is-light">Cancel</a>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
<!-- BEGIN SCRIPTS FOR WYSIWYG EDITOR -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-3.3.1.min.js"><\/script>')</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/trumbowyg.min.js" integrity="sha512-YJgZG+6o3xSc0k5wv774GS+W1gx0vuSI/kr0E0UylL/Qg/noNspPtYwHPN9q6n59CTR/uhgXfjDXLTRI+uIryg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>$('#content').trumbowyg();</script>
<!-- END SCRIPTS FOR WYSIWYG EDITOR -->

```

**PHP Processing**: Implement PHP code to process the form submission and insert a new blog post record into the database.

```php
<?php
// Step 1: Include config.php file

// Step 2: Secure and only allow logged-in users to access this page

/* Step 3: Implement form handling logic to insert the new post into the database. 
   You must update the SQL INSERT statement, and when the record is successfully created, 
   redirect back to the `blog_posts.php` page with the message "The blog post was successfully added."
*/
?>
```

## Create the `blog_post_edit.php` file

**HTML Form**: Add the following HTML form to your `blog_post_edit.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Edit Post</h1>
    <form action="" method="post">
        <!-- ID -->
        <input type="hidden" name="id" value="<?= $post['id'] ?>">
        <!-- Title -->
        <div class="field">
            <label class="label">Title</label>
            <div class="control">
                <input class="input" type="text" name="title" value="<?= $post['title'] ?>" required>
            </div>
        </div>
        <!-- Content -->
        <div class="field">
            <label class="label">Content</label>
            <div class="control">
                <textarea class="textarea" id="content" name="content" required><?= $post['content'] ?></textarea>
            </div>
        </div>
        <!-- Submit -->
        <div class="field is-grouped">
            <div class="control">
                <button type="submit" class="button is-link">Update Post</button>
            </div>
            <div class="control">
                <a href="blog_posts.php" class="button is-link is-light">Cancel</a>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
<!-- BEGIN SCRIPTS FOR WYSIWYG EDITOR -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-3.3.1.min.js"><\/script>')</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/trumbowyg.min.js" integrity="sha512-YJgZG+6o3xSc0k5wv774GS+W1gx0vuSI/kr0E0UylL/Qg/noNspPtYwHPN9q6n59CTR/uhgXfjDXLTRI+uIryg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>$('#content').trumbowyg();</script>
<!-- END SCRIPTS FOR WYSIWYG EDITOR -->

```

**PHP Processing**: Implement PHP code to retrieve the post data from the database and update the post record based on the form submission.

```php
<?php
// Step 1: Include config.php file

// Step 2: Secure and only allow logged-in users to access this page

// Step 3: Check if the update form was submitted. If so, update post details using an UPDATE SQL query

// Step 4: Else it's an initial page request, fetch the post's current data from the database by preparing and executing a SQL statement that uses the post id from the query string (ex. $_GET['id'])

?>
```

## Create the `blog_post_delete.php` file

**HTML Confirmation**: Add the following HTML confirmation to your `blog_post_delete.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Delete Blog Post</h1>
    <p class="subtitle">Are you sure you want to delete the post: <?= $post['title'] ?></p>
    <div class="buttons">
        <a href="?id=<?= $post['id'] ?>&confirm=yes" class="button is-success">Yes</a>
        <a href="blog_posts.php" class="button is-danger">No</a>
    </div>
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Add PHP code to delete a blog post record from the database when the delete confirmation is submitted.

```php
<?php
// Step 1: Include config.php file

// Step 2: Secure and only allow logged-in users to access this page

// Step 3: Check if the $_GET['id'] exists; if it does, get the post record from the database and store it in the associative array $post. If a post with that ID does not exist, display the message "A post with that ID did not exist."

// Step 4: Check if $_GET['confirm'] == 'yes'. This means they clicked the 'yes' button to confirm the removal of the record. Prepare and execute a SQL DELETE statement where the post id == the $_GET['id']. Else (meaning they clicked 'no'), return them to the blog_posts.php page.

?>
```

## Update the `nav.php` navigation template file

**nav.php**: Update the navigation menu to include the `Blog` menu and the link for managing blog posts. The code should be added to the `navbar-start` section of the main navbar after the `home` and `about` links.

```php
<!-- BEGIN ADMIN MENU -->
...
  <a href="blog_posts.php" class="navbar-item">
    Manage BLOG Posts
  </a>
...
<!-- END ADMIN MENU -->
```

## Update the `index.php` file

**HTML Structure**: Add the following HTML structure to your `index.php` file to display the blog posts.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Blog Posts</h1>
    <!-- Posts List -->
    <?php foreach ($posts as $post) : ?>
        <div class="box">
            <article class="media">
                <div class="media-content">
                    <div class="content">
                        <p>
                        <h4 class="title is-4"><a href="blog_post.php?id=<?= $post['id']?>"><?= $post['title'] ?></a></h4>
                        <?= mb_substr($post['content'], 0, 200) . (mb_strlen($post['content']) > 200 ? "<a href=blog_post.php?id={$post['id']}> read more...</a>": "") ?>
                        </p>
                        <p>
                            <small><strong>Author: <?= $post['author'] ?></strong>
                                | Published: <?= time_ago($post['created_at']) ?>
                                <?php if ($post['modified_on'] !== NULL) : ?>
                                    | Updated: <?= time_ago($post['modified_on']) ?>
                                <?php endif; ?>
                            </small>
                        </p>
                    </div>
                    <p class="buttons">
                        <a class="button is-small is-rounded">
                            <span class="icon is-small">
                                <i class="fas fa-thumbs-up"></i>
                            </span>
                            <span><?= $post['likes_count'] ?></span>
                        </a>
                        <a class="button is-small is-rounded">
                            <span class="icon is-small">
                                <i class="fas fa-star"></i>
                            </span>
                            <span><?= $post['favs_count'] ?></span>
                        </a>
                        <a class="button is-small is-rounded">
                            <span class="icon is-small">
                                <i class="fas fa-comment"></i>
                            </span>
                            <span><?= $post['comments_count'] ?></span>
                        </a>
                    </p>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
    </div>
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Add PHP code to the top of the `index.php` file to fetch the blog posts from the database and display them on the page.

```php
<?php
// Include config.php file
include 'config.php';

// Prepare the SQL query to select all posts from the database that are published and sort them in reverse chronological order (DESC)
$stmt = $pdo->prepare('SELECT blog_posts.*, users.full_name AS author FROM blog_posts JOIN users ON blog_posts.author_id = users.id WHERE is_published = 1 ORDER BY blog_posts.created_at DESC');

// Execute the query
$stmt->execute();

// Fetch and store the results in the $posts associative array
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the query returned any rows. If not, display a message.
if (!$posts) {
    $_SESSION['messages'][] = "There are no blog posts in the database.";
}
?>
```

## Final Steps

- Test your application thoroughly to catch and fix any bugs or issues.
- Ensure all files are correctly added and committed to your repository before pushing.
- Stage, commit, and push your final changes to GitHub.
- Submit your project URL as previously instructed, ensuring your GitHub repository is up to date so it can be accessed and evaluated.

## Conclusion

This simple blog system provides essential functionalities for users to manage their blog posts efficiently. By following these steps, you can create, read, update, and delete blog posts, providing a platform for users to share their thoughts and ideas through blogging. By adding the `index.php` page, your simple blog system now has a public-facing interface where visitors can view the latest blog posts. This enhances the functionality of your web application and provides a platform for sharing information with a broader audience.

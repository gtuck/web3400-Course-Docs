# Simple Content Management System (Project 04)

In this project, you will develop a simple content management system that allows users to create, read, update, and delete articles. The system will also include user authentication, allowing only logged-in users to create and manage their articles. You'll follow a structured approach to ensure the content management system functions correctly and provides a good user experience.

## Copy Project 03 to the Project 04 folder

- Recursively copy the project folder.
- Stage, commit, and push your new project to GitHub.

## Set Up the Database

Before coding, you must set up the database table to store the articles. Use the following SQL statement to create the `articles` table:

```sql
-- Table structure for table `articles`
CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `comments_count` mediumint(3) NOT NULL DEFAULT 0,
  `likes_count` mediumint(3) NOT NULL DEFAULT 0,
  `favs_count` mediumint(3) NOT NULL DEFAULT 0,
  `modified_on` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Indexes for table `articles`
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);
  
-- AUTO_INCREMENT for table `articles`
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

```

## Update the `config.php` file

**PHP**: Open your `config.php` file and add the following function at the end of the file:

```php
function time_ago($datetime)
{
    $time_ago = strtotime($datetime);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;

    $minutes = round($seconds / 60);       // value 60 is seconds
    $hours   = round($seconds / 3600);     // value 3600 is 60 minutes * 60 sec
    $days    = round($seconds / 86400);    // value 86400 is 24 hours * 60 minutes * 60 sec
    $weeks   = round($seconds / 604800);   // value 604800 is 7 days * 24 hours * 60 minutes * 60 sec
    $months  = round($seconds / 2629440);  // value 2629440 is ((365+365+365+365+366)/5/12) days * 24 hours * 60 minutes * 60 sec
    $years   = round($seconds / 31553280); // value 31553280 is ((365+365+365+365+366)/5) days * 24 hours * 60 minutes * 60 sec

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

**Function Usage**: You can now use the `time_ago` function in any PHP file that includes `config.php`. For example, you can display the publication date of an article like this:

```php
echo time_ago($article['created_at']);
```

## Create the `article.php` file

**HTML Structure**: Add the following HTML structure to your `article.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title"><?= $article['title'] ?></h1>
    <div class="box">
        <article class="media">
            <figure class="media-left">
                <p class="image is-128x128">
                    <img src="https://source.unsplash.com/random/128x128/?wellness">
                </p>
            </figure>
            <div class="media-content">
                <div class="content">
                    <p>
                        <?= $article['content'] ?>
                    </p>
                    <p>
                        <small><strong>Author: <?= $article['author'] ?></strong>
                            | Published: <?= time_ago($article['created_at']) ?>
                            <?php if ($article['modified_on'] !== NULL) : ?>
                                | Updated: <?= time_ago($article['modified_on']) ?>
                            <?php endif; ?>
                        </small>
                    </p>
                </div>
                <p class="buttons">
                    <a href="contact.php" class="button is-small is-info is-rounded">
                        <span class="icon">
                            <i class="fas fa-lg fa-hiking"></i>
                        </span>
                        <span><strong>Begin your journey now</strong></span>
                    </a>
                </p>
                <p class="buttons">
                    <a class="button is-small is-rounded">
                        <span class="icon is-small">
                            <i class="fas fa-thumbs-up"></i>
                        </span>
                        <span><?= $article['likes_count'] ?></span>
                    </a>
                    <a class="button is-small is-rounded">
                        <span class="icon is-small">
                            <i class="fas fa-star"></i>
                        </span>
                        <span><?= $article['favs_count'] ?></span>
                    </a>
                    <a class="button is-small is-rounded">
                        <span class="icon is-small">
                            <i class="fas fa-comment"></i>
                        </span>
                        <span><?= $article['comments_count'] ?></span>
                    </a>
                </p>
            </div>
        </article>
    </div>
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Complete the following coding steps by adding your code to the top of the `article.php` file. Your finished file will fetch articles from the database and populate the articles table dynamically.

```php
<?php
// Step 1: Include config.php file

// Step 2: Check if the $_GET['id'] exists; if it does, get the article record from the database and store it in the associative array named $article.
// SQL example: SELECT articles.*, users.full_name AS author FROM articles JOIN users ON articles.author_id = users.id WHERE is_published = 1 AND articles.id = ?

// Step 3: If an article with that ID does not exist, display the message "An article with that ID did not exist."
?>
```

## Create the `articles.php` file

**HTML Structure**: Add the following HTML structure to your `articles.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Articles</h1>
    <!-- Add Post Button -->
    <div class="buttons">
        <a href="article_add.php" class="button is-link">Write an article</a>
    </div>
    <!-- Posts Table -->
    <table class="table is-bordered is-striped is-hoverable is-fullwidth">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Content</th>
                <th>Author</th>
                <th><small>Featured | Published | Edit | Del</small></th>
            </tr>
        </thead>
        <tbody>
            <!-- Fetch Posts from Database and Populate Table Rows Dynamically -->
            <?php foreach ($articles as $article) : ?>
                <tr>
                    <td><?= $article['id'] ?></td>
                    <td><a href="article.php?id=<?= $article['id'] ?>"><?= mb_substr($article['title'], 0, 30) . (mb_strlen($article['title']) > 30 ? '...' : '') ?></a></td>
                    <td><?= mb_substr($article['content'], 0, 50) . (mb_strlen($article['content']) > 50 ? '...' : '') ?></td>
                    <td><?= $article['author'] ?></td>
                    <td>
                        <!-- Feature Link -->
                        <?php if ($article['is_featured'] == 1) : ?>
                            <a href="articles.php?id=<?= $article['id'] ?>&is_featured=1" class="button is-warning">
                                <i class="fas fa-lg fa-check-circle"></i>
                            </a>
                        <?php else : ?>
                            <a href="articles.php?id=<?= $article['id'] ?>&is_featured=0" class="button is-warning is-light">
                                <i class="fas fa-lg fa-times-circle"></i>
                            </a>
                        <?php endif; ?>
                        <!-- Publish Link -->
                        <?php if ($article['is_published'] == 1) : ?>
                            <a href="articles.php?id=<?= $article['id'] ?>&is_published=1" class="button is-primary">
                                <i class="fas fa-lg fa-check-circle"></i>
                            </a>
                        <?php else : ?>
                            <a href="articles.php?id=<?= $article['id'] ?>&is_published=0" class="button is-primary is-light">
                                <i class="fas fa-lg fa-times-circle"></i>
                            </a>
                        <?php endif; ?>
                        <!-- Edit Post Link -->
                        <a href="article_edit.php?id=<?= $article['id'] ?>" class="button is-info">
                            <i class="fas fa-lg fa-edit"></i>
                        </a>
                        <!-- Delete Post Form -->
                        <a href="article_delete.php?id=<?= $article['id'] ?>" class="button is-danger">
                            <i class="fas fa-lg fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<!-- END YOUR CONTENT -->
```

**Note:** In this code, the `mb_substr` function is used to extract the first 30 characters of the title and the first 50 characters of the content. The ternary operator `(mb_strlen($article['title']) > 30 ? '...' : '')` is used to append an ellipsis (`...`) if the

 original string is longer than the specified length, indicating that the text has been truncated.

**PHP Processing**: Complete the following coding steps by adding your code to the top of the `articles.php` file. Your finished file will fetch articles from the database and populate the articles table dynamically.

```php
<?php
// Step 1: Include config.php file

// Step 2: Secure and only allow 'admin' users to access this page

// Step 3: Prepare the SQL query template to select all posts from the database
// ex. $stmt = $pdo->prepare('SELECT articles.*, users.full_name AS author FROM articles JOIN users ON articles.author_id = users.id ORDER BY `created_at` DESC');

// Step 4: Execute the query
// ex. $stmt->execute();

// Step 5: Fetch and store the results in the $articles associative array
// ex. $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Step 6: Check if the query returned any rows. If not, display the message: "There are no articles in the database."
// ex. if (!$articles) {...}

// Step 7: If the 'is_published' control is clicked, toggle the status from 0 -> 1 for published or 1 -> 0 for unpublished

// Step 8: If the 'is_featured' control is clicked, toggle the status from 0 -> 1 for featured or 1 -> 0 for unfeatured
?>
```

## Create the `article_add.php` file

**HTML Form**: Add the following HTML form to your `article_add.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Write an article</h1>
    <form action="" method="post">
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
                <a href="articles.php" class="button is-link is-light">Cancel</a>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Implement PHP code to process the form submission and insert a new article record into the database.

```php
<?php
// Step 1: Include config.php file

// Step 2: Secure and only allow 'admin' users to access this page

/* Step 3: Implement form handling logic to insert the new article into the database. 
   You must update the SQL INSERT statement, and when the record is successfully created, 
   redirect back to the `articles.php` page with the message "The article was successfully added."
*/
?>
```

## Create the `article_edit.php` file

**HTML Form**: Add the following HTML form to your `article_edit.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Edit Article</h1>
    <form action="" method="post">
        <!-- ID -->
        <input type="hidden" name="id" value="<?= $article['id'] ?>">
        <!-- Title -->
        <div class="field">
            <label class="label">Title</label>
            <div class="control">
                <input class="input" type="text" name="title" value="<?= $article['title'] ?>" required>
            </div>
        </div>
        <!-- Content -->
        <div class="field">
            <label class="label">Content</label>
            <div class="control">
                <textarea class="textarea" id="content" name="content" required><?= $article['content'] ?></textarea>
            </div>
        </div>
        <!-- Submit -->
        <div class="field is-grouped">
            <div class="control">
                <button type="submit" class="button is-link">Update Article</button>
            </div>
            <div class="control">
                <a href="articles.php" class="button is-link is-light">Cancel</a>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Implement PHP code to retrieve the article data from the database and update the article record based on the form submission.

```php
<?php
// Step 1: Include config.php file

// Step 2: Secure and only allow 'admin' users to access this page

// Step 3: Check if the update form was submitted. If so, update article details using an UPDATE SQL query.

// Step 4: Else it's an initial page request, fetch the article's current data from the database by preparing and executing a SQL statement that uses the article id from the query string (ex. $_GET['id'])

?>
```

## Create the `article_delete.php` file

**HTML Confirmation**: Add the following HTML confirmation to your `article_delete.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Delete Article</h1>
    <p class="subtitle">Are you sure you want to delete the article: <?= $article['title'] ?></p>
    <div class="buttons">
        <a href="?id=<?= $article['id'] ?>&confirm=yes" class="button is-success">Yes</a>
        <a href="articles.php" class="button is-danger">No</a>
    </div>
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Add PHP code to delete an article record from the database when the delete confirmation is submitted.

```php
<?php
// Step 1: Include config.php file

// Step 2: Secure and only allow 'admin' users to access this page

// Step 3: Check if the $_GET['id'] exists; if it does, get the article record from the database and store it in the associative array $article. If an article with that ID does not exist, display "An article with that ID did not exist."

// Step 4: Check if $_GET['confirm'] == 'yes'. This means they clicked the 'yes' button to confirm the removal of the record. Prepare and execute a SQL DELETE statement where the article id == the $_GET['id']. Else (meaning they clicked 'no'), return them to the articles.php page.

?>
```

## Update the `nav.php` navigation template file

**nav.php**: Update the navigation menu to include the `Articles` menu and the link for managing articles. The code should be added to the `navbar-start` section of the main navbar after the `home` and `about` links. Also, update the hero section, replacing the placeholder text with content related to the theme of your site.

```html
<!-- BEGIN ADMIN MENU -->
...
  <a href="articles.php" class="navbar-item">
    Manage Articles
  </a>
...
<!-- END ADMIN MENU -->
...
<!-- BEGIN HERO -->
  <section class="hero is-info">
    <div class="hero-body">
      <p class="title">
        Your hook goes here
      </p>
      <p class="subtitle">
        Your catchy subtitle goes here...
      </p>
      <a href="contact.php" class="button is-medium is-info is-light is-rounded">
        <span class="icon is-large">
          <i class="fab fa-2x fa-pagelines"></i>
        </span>
        <span>Your call to action goes here</span>
      </a>
    </div>
  </section>
<!-- END HERO -->
```

## Update the `index.php` file

**HTML Structure**: Add the following HTML structure to your index.php file to display the articles.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Featured Articles</h1>
    <!-- articles List -->
    <?php foreach ($articles as $article) : ?>
        <div class="box">
            <article class="media">
                <figure class="media-left">
                    <p class="image is-128x128">
                        <img class="is-rounded" src="https://source.unsplash.com/random/128x128/?sig=<?= $article['id'] ?>&wellness">
                    </p>
                </figure>
                <div class="media-content">
                    <div class="content">
                        <p>
                        <h4 class="title is-4"><a href="article.php?id=<?= $article['id'] ?>"><?= $article['title'] ?></a></h4>
                        <?= mb_substr($article['content'], 0, 200) . (mb_strlen($article['content']) > 200 ? "<a href=article.php?id={$article['id']}><strong> read more...</strong></a>" : "") ?>
                        </p>
                        <p>
                            <small><strong>Author: <?= $article['author'] ?></strong>
                                | Published: <?= time_ago($article['created_at']) ?>
                                <?php if ($article['modified_on'] !== NULL) : ?>
                                    | Updated: <?= time_ago($article['modified_on']) ?>
                                <?php endif; ?>
                            </small>
                        </p>
                    </div>
                    <p class="buttons">
                        <a class="button is-small is-rounded">
                            <span class="icon is-small">
                                <i class="fas fa-thumbs-up"></i>
                            </span>
                            <span><?= $article['likes_count'] ?></span>
                        </a>
                        <a class="button is-small is-rounded">
                            <span class="icon is-small">
                                <i class="fas fa-star"></i>
                            </span>
                            <span><?= $article['favs_count'] ?></span>
                        </a>
                        <a class="button is-small is-rounded">
                            <span class="icon is-small">
                                <i class="fas fa-comment"></i>
                            </span>
                            <span><?= $article['comments_count'] ?></span>
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

**PHP Processing**: Add PHP code to the top of the `index.php` file to fetch the articles from the database and display them on the page.

```php
<?php
// Include config.php file
include 'config.php';

// Prepare the SQL query to select all articles from the database that are published and sort them in reverse chronological order (DESC)
$stmt = $pdo->prepare('SELECT articles.*, users.full_name AS author FROM articles JOIN users ON articles.author_id = users.id WHERE is_published = 1 AND is_featured = 1 ORDER BY articles.created_at DESC LIMIT 10');

// Execute the query
$stmt->execute();

// Fetch and store the results in the $articles associative array
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the query returned any rows. If not, display a message.
if (!$articles) {
    $_SESSION['messages'][] = "There are no articles in the database.";
}
?>
```

## Final Steps

- Test your application thoroughly to catch and fix any bugs or issues.
- Ensure all files are correctly added and committed to your repository before pushing.
- Stage, commit, and push your final changes to GitHub.
- Submit your project URL as previously instructed, ensuring your GitHub repository is up to date so it can be accessed and evaluated.

## Conclusion

This simple content management system provides essential functionalities for users to manage their articles efficiently. By following these steps, you can create, read, update, and delete articles, providing a platform for users to share their thoughts and ideas through articles. By adding the `index.php` page, your simple content management system now has a public-facing interface where visitors can view the latest articles. This enhances the functionality of your web application and provides a platform for sharing information with a broader audience.

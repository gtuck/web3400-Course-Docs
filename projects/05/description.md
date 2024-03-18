# CMS with Enhanced Features (Project 05)

In this project, you will enhance the features of your simple content management system by adding functionality to disable interaction buttons unless the user is logged in, allowing users to like articles, favorite articles, and comment on articles. You will also create a table to record all user interactions and display them on the user's profile page.

## Copy Project 04 to the Project 05 folder

- Recursively copy the project folder.
- Stage, commit, and push your new project to GitHub.

## Update the Database

Add a new table to record user interactions with articles and create SQL triggers that increment the `comments_count`, `likes_count`, and `favs_count` fields in the `articles` table when an insert event occurs in the `user_interactions` table, you can use the following SQL statements:

```sql
-- Table structure for table `user_interactions`
CREATE TABLE `user_interactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `interaction_type` enum('like','favorite','comment') NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Indexes for table `user_interactions`
ALTER TABLE `user_interactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `article_id` (`article_id`);
  
-- AUTO_INCREMENT for table `user_interactions`
ALTER TABLE `user_interactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

-- Trigger to increment comments_count
DELIMITER //
CREATE TRIGGER increment_comments_count
AFTER INSERT ON user_interactions
FOR EACH ROW
BEGIN
    IF NEW.interaction_type = 'comment' THEN
        UPDATE articles
        SET comments_count = comments_count + 1
        WHERE id = NEW.article_id;
    END IF;
END;
//
DELIMITER ;

-- Trigger to increment likes_count
DELIMITER //
CREATE TRIGGER increment_likes_count
AFTER INSERT ON user_interactions
FOR EACH ROW
BEGIN
    IF NEW.interaction_type = 'like' THEN
        UPDATE articles
        SET likes_count = likes_count + 1
        WHERE id = NEW.article_id;
    END IF;
END;
//
DELIMITER ;

-- Trigger to increment favs_count
DELIMITER //
CREATE TRIGGER increment_favs_count
AFTER INSERT ON user_interactions
FOR EACH ROW
BEGIN
    IF NEW.interaction_type = 'favorite' THEN
        UPDATE articles
        SET favs_count = favs_count + 1
        WHERE id = NEW.article_id;
    END IF;
END;
//
DELIMITER ;
```

The triggers are created with the following logic:

- `increment_comments_count` trigger: After a new row is inserted into the `user_interactions` table, if the `interaction_type` is 'comment', the trigger will increment the `comments_count` field for the corresponding article in the `articles` table.
- `increment_likes_count` trigger: Similarly, if the `interaction_type` is 'like', the trigger will increment the `likes_count` field for the corresponding article.
- `increment_favs_count` trigger: If the `interaction_type` is 'favorite', the trigger will increment the `favs_count` field for the corresponding article.

You can execute these SQL statements in your database management tool to create the table and triggers. Once the triggers are set up, they will automatically update the count fields in the `articles` table whenever a new interaction is recorded in the `user_interactions` table.

## Update the `article.php` file

### Disable Interaction Buttons Unless User is Logged In

Toggle the HTML `disabled` attribute for the buttons on the `article.php` page, modify the buttons section:

```html
<p class="buttons">
    <!-- Like Button -->
    <a href="article_like.php?id=<?= $article['id'] ?>" class="button is-small is-rounded" <?= !isset($_SESSION['loggedin']) ? 'disabled' : '' ?>>
        <span class="icon is-small">
            <i class="fas fa-thumbs-up"></i>
        </span>
        <span><?= $article['likes_count'] ?></span>
    </a>
    <!-- Favorite Button -->
    <a href="article_favorite.php?id=<?= $article['id'] ?>" class="button is-small is-rounded" <?= !isset($_SESSION['loggedin']) ? 'disabled' : '' ?>>
        <span class="icon is-small">
            <i class="fas fa-star"></i>
        </span>
        <span><?= $article['favs_count'] ?></span>
    </a>
    <!-- Comments Count -->
    <a href="#comments" class="button is-small is-rounded" <?= !isset($_SESSION['loggedin']) ? 'disabled' : '' ?>>
        <span class="icon is-small">
            <i class="fas fa-comment"></i>
        </span>
        <span><?= $article['comments_count'] ?></span>
    </a>
</p>
```

In this modification, the `disabled` attribute is conditionally added to each button based on whether the user is logged in or not. The PHP ternary operator `!isset($_SESSION['loggedin']) ? 'disabled' : ''` is used to check if the `loggedin` session variable is set. If the user is not logged in (`!isset($_SESSION['loggedin'])` evaluates to `true`), the `disabled` attribute is added to the button, making it non-interactive. If the user is logged in, the `disabled` attribute is omitted, allowing the button to be clicked.

### Display Comments and Add Comment Form

At the bottom of the `article.php` file, add the following section to display comments and a form for adding comments:

```html
<!-- Comments Section -->
<section id="comments" class="section">
    <h2 class="title is-4">Comments</h2>
    <!-- Display the five most recent comments -->
    <?php foreach ($comments as $comment): ?>
        <article class="media">
            <div class="media-content">
                <div class="content">
                    <p>
                        <strong><?= $comment['user_name'] ?></strong>
                        <br>
                        <?= $comment['comment'] ?>
                        <br>
                        <small><?= time_ago($comment['created_at']) ?></small>
                    </p>
                </div>
            </div>
        </article>
    <?php endforeach; ?>

    <!-- Comment Form -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <form action="article_comment.php" method="post">
            <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
            <div class="field">
                <label class="label">Add a comment</label>
                <div class="control">
                    <textarea class="textarea" name="comment" required></textarea>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button type="submit" class="button is-primary">Submit Comment</button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</section>
```

### PHP Processing for Comments

At the top of the `article.php` file, add PHP code to fetch the comments for the article from the database:

```php
<?php
// Fetch comments for the article
$stmt = $pdo->prepare('SELECT user_interactions.comment, user_interactions.created_at, users.full_name AS user_name FROM user_interactions JOIN users ON user_interactions.user_id = users.id WHERE user_interactions.article_id = ? AND user_interactions.interaction_type = "comment" ORDER BY user_interactions.created_at DESC LIMIT 5');
$stmt->execute([$article['id']]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

## Create PHP Handlers for Likes and Favorites

### `article_like.php`

Create a file named `article_like.php` to handle likes:

```php
<?php
include 'config.php';

// Check if user is logged in and article ID is provided
if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
    $user_id = $_SESSION['user_id'];
    $article_id = $_GET['id'];

    // Insert a new like into the user_interactions table
    $stmt = $pdo->prepare('INSERT INTO user_interactions (user_id, article_id, interaction_type) VALUES (?, ?, "like")');
    $stmt->execute([$user_id, $article_id]);

    // Redirect back to the article page
    header('Location: article.php?id=' . $article_id);
    exit;
}

// Redirect to login page if not logged in
header('Location: login.php');
exit;
?>
```

### `article_favorite.php`

Create a file named `article_favorite.php` to handle favorites:

```php
<?php
include 'config.php';

// Check if user is logged in and article ID is provided
if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
    $user_id = $_SESSION['user_id'];
    $article_id = $_GET['id'];

    // Insert a new favorite into the user_interactions table
    $stmt = $pdo->prepare('INSERT INTO user_interactions (user_id, article_id, interaction_type) VALUES (?, ?, "favorite")');
    $stmt->execute([$user_id, $article_id]);

    // Redirect back to the article page
    header('Location: article.php?id=' . $article_id);
    exit;
}

// Redirect to login page if not logged in
header('Location: login.php');
exit;
?>
```

## Create PHP Handler for Comments

### `article_comment.php`

Create a file named `article_comment.php` to handle comments:

```php
<?php
include 'config.php';

// Check if user is logged in, article ID and comment are provided
if (isset($_SESSION['user_id']) && isset($_POST['article_id']) && isset($_POST['comment'])) {
    $user_id = $_SESSION['user_id'];
    $article_id = $_POST['article_id'];
    $comment = trim($_POST['comment']);

    // Insert the comment into the user_interactions table
    $stmt = $pdo->prepare('INSERT INTO user_interactions (user_id, article_id, interaction_type, comment) VALUES (?, ?, "comment", ?)');
    $stmt->execute([$user_id, $article_id, $comment]);

    // Redirect back to the article page
    header('Location: article.php?id=' . $article_id);
    exit;
}

// Redirect to login page if not logged in or missing data
header('Location: login.php');
exit;
?>
```

## Update the User Profile Page

On the user profile page (`profile.php`), add a section to display the user's interactions:

```php
<section class="section">
    <h2 class="title is-4">My Interactions</h2>
    <?php foreach ($interactions as $interaction): ?>
        <div class="box">
            <article class="media">
                <div class="media-content">
                    <div class="content">
                        <p>
                            <strong><?= ucfirst($interaction['interaction_type']) ?>:</strong>
                            <?php if ($interaction['interaction_type'] === 'comment'): ?>
                                <?= $interaction['comment'] ?>
                            <?php else: ?>
                                <a href="article.php?id=<?= $interaction['article_id'] ?>">Article #<?= $interaction['article_id'] ?></a>
                            <?php endif; ?>
                            <br>
                            <small><?= time_ago($interaction['created_at']) ?></small>
                        </p>
                    </div>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
</section>
```

Add PHP code at the top of the `profile.php` file to fetch the user's interactions from the database:

```php
<?php
// Fetch user interactions
$stmt = $pdo->prepare('SELECT * FROM user_interactions WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$interactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

## Final Steps

- Test your application thoroughly to catch and fix any bugs or issues.
- Ensure all files are correctly added and committed to your repository before pushing.
- Stage, commit, and push your final changes to GitHub.
- Submit your project URL as previously instructed

, ensuring your GitHub repository is up to date so it can be accessed and evaluated.

## Conclusion

With these enhancements, your simple content management system now supports more interactive features, allowing users to like articles, mark them as favorites, and leave comments. Additionally, all user interactions are recorded and displayed on the user's profile page, providing a more engaging and personalized experience for users of your system.

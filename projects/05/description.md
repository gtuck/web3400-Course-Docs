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
  <!-- Comment Form -->
  <?php if (isset($_SESSION['user_id'])) : ?>
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
  <hr>
  <h2 class="title is-4">Comments</h2>
  <!-- Display the five most recent comments -->
  <?php foreach ($comments as $comment) : ?>
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
</section>
```

### PHP for getting the comments

At the top of the `article.php` file, add PHP code to fetch the comments for the article from the database in the same `if` statment (but after) where you are getting the article record:

```php
// Fetch comments for the article
$stmt = $pdo->prepare('SELECT user_interactions.comment, user_interactions.created_at, users.full_name AS user_name FROM user_interactions JOIN users ON user_interactions.user_id = users.id WHERE user_interactions.article_id = ? AND user_interactions.interaction_type = "comment" ORDER BY user_interactions.created_at DESC LIMIT 5');
$stmt->execute([$article['id']]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

## Create PHP Handlers for Likes and Favorites

### `article_like.php`

Create a file named `article_like.php` to handle likes: Ensure that a user can only like an article once:

```php
<?php
include 'config.php';

// Check if user is logged in and article ID is provided
if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
    $user_id = $_SESSION['user_id'];
    $article_id = $_GET['id'];

    // Check if the user has already liked the article
    $stmt = $pdo->prepare('SELECT id FROM user_interactions WHERE user_id = ? AND article_id = ? AND interaction_type = "like"');
    $stmt->execute([$user_id, $article_id]);
    $like_exists = $stmt->fetch();

    // Only insert a new like if it does not exist
    if (!$like_exists) {
        $stmt = $pdo->prepare('INSERT INTO user_interactions (user_id, article_id, interaction_type) VALUES (?, ?, "like")');
        $stmt->execute([$user_id, $article_id]);
    } else {
        $_SESSION['messages'][] = "You have already liked this article.";
    }

    // Redirect back to the article page
    header('Location: article.php?id=' . $article_id);
    exit;
}

// Redirect to login page if not logged in
header('Location: login.php');
exit;
?>
```

In this `article_like.php`, the script checks if a like entry for the same user and article already exists before inserting a new like into the `user_interactions` table. If the `$like_exists` variable is false (meaning there is no existing like), the script proceeds to insert the new like. Otherwise, it skips the insertion and displays the message 'You have already liked this article.'. This way, a user can only like an article once.

### `article_favorite.php`

Create a file named `article_favorite.php` to handle favorites. Ensure that a user can only favorite an article once:

```php
<?php
include 'config.php';

// Check if user is logged in and article ID is provided
if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
    $user_id = $_SESSION['user_id'];
    $article_id = $_GET['id'];

    // Check if the user has already favorited the article
    $stmt = $pdo->prepare('SELECT id FROM user_interactions WHERE user_id = ? AND article_id = ? AND interaction_type = "favorite"');
    $stmt->execute([$user_id, $article_id]);
    $favorite_exists = $stmt->fetch();

    // Only insert a new favorite if it does not exist
    if (!$favorite_exists) {
        $stmt = $pdo->prepare('INSERT INTO user_interactions (user_id, article_id, interaction_type) VALUES (?, ?, "favorite")');
        $stmt->execute([$user_id, $article_id]);
    } else {
        $_SESSION['messages'][] = "You have already fav'd this article.";
    }

    // Redirect back to the article page
    header('Location: article.php?id=' . $article_id);
    exit;
}

// Redirect to login page if not logged in
header('Location: login.php');
exit;

?>
```

In this `article_favorite.php`, before inserting a new favorite into the `user_interactions` table, the script first checks if a favorite entry for the same user and article already exists. It does this by preparing and executing a SELECT query to look for an existing favorite. If the `$favorite_exists` variable is false (meaning there is no existing favorite), the script proceeds to insert the new favorite. Otherwise, it skips the insertion and displays the message 'You have already fav'd this article.'. This way, a user can only like an article once.

## Create PHP Handler for Comments

### `article_comment.php`

Create a file named `article_comment.php` to handle comments. Restrict a user to commenting on an article a maximum of three times:

```php
<?php
include 'config.php';

// Check if user is logged in, article ID and comment are provided
if (isset($_SESSION['user_id']) && isset($_POST['article_id']) && isset($_POST['comment'])) {
    $user_id = $_SESSION['user_id'];
    $article_id = $_POST['article_id'];
    $comment = trim($_POST['comment']);

    // Check how many comments the user has already made on the article
    $stmt = $pdo->prepare('SELECT COUNT(*) AS comment_count FROM user_interactions WHERE user_id = ? AND article_id = ? AND interaction_type = "comment"');
    $stmt->execute([$user_id, $article_id]);
    $comment_count = $stmt->fetchColumn();

    // Only insert a new comment if the user has not exceeded the limit
    if ($comment_count < 3) {
        $stmt = $pdo->prepare('INSERT INTO user_interactions (user_id, article_id, interaction_type, comment) VALUES (?, ?, "comment", ?)');
        $stmt->execute([$user_id, $article_id, $comment]);
    } else {
        $_SESSION['messages'][] = "You have reached your three comment limit for this article.";
    }

    // Redirect back to the article page
    header('Location: article.php?id=' . $article_id);
    exit;
}

// Redirect to login page if not logged in or missing data
header('Location: login.php');
exit;
?>
```

In this `article_comment.php`, the script first counts the number of comments the user has already made on the article by executing a SELECT query that counts rows in the `user_interactions` table where the `user_id`, `article_id`, and `interaction_type` match the current user, article, and "comment" type, respectively. If the count (`$comment_count`) is less than 3, the script proceeds to insert the new comment. Otherwise, it skips the insertion and displays the message 'You have reached your three comment limit for this article.', effectively limiting the user to a maximum of three comments on the article.

## Update the User Profile Page

At the bottom of the user profile page (`profile.php`), add a section to display the user's interactions:

```html
<!-- My interactions section -->
<section class="section">
  <h2 class="title is-4">My Interactions</h2>
  <?php foreach ($interactions as $interaction) : ?>
  <div class="box">
    <article class="media">
      <div class="media-content">
        <div class="content">
          <p>
            <strong><?= ucfirst($interaction['interaction_type']) ?>:</strong>
            <?php if ($interaction['interaction_type'] === 'comment') : ?>
            <a href="article.php?id=<?= $interaction['article_id'] ?>"><?= $interaction['article_title'] ?></a>
            <?php else : ?>
            <a href="article.php?id=<?= $interaction['article_id'] ?>"><?= $interaction['article_title'] ?></a>
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

Add PHP code at the top of the `profile.php` file to fetch the user's interactions from the database in the `try` statment (but after) where you are getting the users record:

```php
// Fetch user interactions along with article titles
    $stmt = $pdo->prepare('SELECT user_interactions.*, articles.title AS article_title FROM user_interactions JOIN articles ON user_interactions.article_id = articles.id WHERE user_id = ? ORDER BY user_interactions.created_at DESC');
    $stmt->execute([$_SESSION['user_id']]);
    $interactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

## Update the index.php Page

To update the `index.php` page add the `is-static` attribute to the interaction buttons, you can modify the section where the buttons are defined as follows:

```html
...
<p class="buttons">
  <a class="button is-small is-rounded is-static">
    <span class="icon is-small">
      <i class="fas fa-thumbs-up"></i>
    </span>
    <span><?= $article['likes_count'] ?></span>
  </a>
  <a class="button is-small is-rounded is-static">
    <span class="icon is-small">
      <i class="fas fa-star"></i>
    </span>
    <span><?= $article['favs_count'] ?></span>
  </a>
  <a class="button is-small is-rounded is-static">
    <span class="icon is-small">
      <i class="fas fa-comment"></i>
    </span>
    <span><?= $article['comments_count'] ?></span>
  </a>
</p>
...
```

The `is-static` attribute makes the buttons appear static (non-clickable) and is useful for displaying information that does not require interaction, such as the count of likes, favorites, and comments for each article on the `index.php` page.

## Final Steps

- Test your application thoroughly to catch and fix any bugs or issues.
- Ensure all files are correctly added and committed to your repository before pushing.
- Stage, commit, and push your final changes to GitHub.
- Submit your project URL as previously instructed

, ensuring your GitHub repository is up to date so it can be accessed and evaluated.

## Conclusion

With these enhancements, your simple content management system now supports more interactive features, allowing users to like articles, mark them as favorites, and leave comments. Additionally, all user interactions are recorded and displayed on the user's profile page, providing a more engaging and personalized experience for users of your system.

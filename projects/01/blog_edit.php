<?php
/*
  Admin: Edit Post
  - Loads a post by `post_id`, validates edits, and saves changes
  - If the title is changed, regenerate a unique slug
*/

require __DIR__ . '/config.php';

// Validate and load the target post
$post_id = (int)($_GET['post_id'] ?? 0);
if ($post_id <= 0) { flash('Invalid post id.', 'is-danger'); header('Location: admin_blog.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? LIMIT 1");
$stmt->execute([$post_id]);
$post = $stmt->fetch();
if (!$post) { flash('Post not found.', 'is-danger'); header('Location: admin_blog.php'); exit; }

$pageTitle = 'Edit: ' . $post['title'] . ' - ' . ($siteName ?? 'Site');

// Initialize form state
$errors = [];
$title = $post['title'];
$body  = $post['body'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Normalize inputs
  $title = trim($_POST['title'] ?? '');
  $body  = trim($_POST['body'] ?? '');

  // Validation rules
  if ($title === '' || mb_strlen($title) < 3) {
    $errors['title'] = 'Title is required (min 3 chars).';
  }
  if ($body === '' || mb_strlen($body) < 10) {
    $errors['body'] = 'Body is required (min 10 chars).';
  }

  if (!$errors) {
    // If title changed, recompute a slug and ensure uniqueness, excluding this record
    $slug = $post['slug'];
    if ($title !== $post['title']) {
      $base = slugify($title);
      $slug = $base;
      $i = 2;
      $check = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE slug = ? AND id <> ?");
      while (true) {
        $check->execute([$slug, $post_id]);
        if ($check->fetchColumn() == 0) break;
        $slug = $base . '-' . $i++;
      }
    }

    // Persist the changes
    $upd = $pdo->prepare("UPDATE posts SET title = ?, slug = ?, body = ? WHERE id = ?");
    $upd->execute([$title, $slug, $body, $post_id]);

    flash('Post updated.', 'is-success');
    header('Location: admin_blog.php'); exit;
  }
}
?>
<?php require __DIR__ . '/templates/head.php'; ?>
<?php require __DIR__ . '/templates/nav.php'; ?>
<?php require __DIR__ . '/templates/flash.php'; ?>

<section class="section">
  <div class="container">
    <h1 class="title">Edit Post</h1>

    <form method="post" class="box">
      <div class="field">
        <label class="label" for="title">Title</label>
        <div class="control">
          <input class="input <?= isset($errors['title']) ? 'is-danger' : '' ?>" type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
        </div>
        <?php if (isset($errors['title'])): ?><p class="help is-danger"><?= htmlspecialchars($errors['title']) ?></p><?php endif; ?>
      </div>

      <div class="field">
        <label class="label" for="body">Body</label>
        <div class="control">
          <textarea class="textarea <?= isset($errors['body']) ? 'is-danger' : '' ?>" id="body" name="body" rows="10" required><?= htmlspecialchars($body) ?></textarea>
        </div>
        <?php if (isset($errors['body'])): ?><p class="help is-danger"><?= htmlspecialchars($errors['body']) ?></p><?php endif; ?>
      </div>

      <div class="field is-grouped">
        <div class="control"><button class="button is-link" type="submit">Save Changes</button></div>
        <div class="control"><a class="button is-light" href="admin_blog.php">Cancel</a></div>
      </div>
    </form>
  </div>
</section>

<?php require __DIR__ . '/templates/footer.php'; ?>

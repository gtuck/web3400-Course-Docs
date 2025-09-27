<?php
/*
  Admin: Delete Post
  - Confirmation step before deleting a post
  - Uses PRG: POST to delete, then redirect with a flash message
*/

require __DIR__ . '/config.php';

// EXAMPLE: QUERY_PARAM — Accept `post_id` from GET (first load) or POST (confirm submit)
$post_id = (int)($_GET['post_id'] ?? ($_POST['post_id'] ?? 0));
if ($post_id <= 0) { flash('Invalid post id.', 'is-danger'); header('Location: admin_blog.php'); exit; }

// EXAMPLE: PREPARED_SELECT — Load minimal post info for confirmation UI
$stmt = $pdo->prepare("SELECT id, title, created_at FROM posts WHERE id = ? LIMIT 1");
$stmt->execute([$post_id]);
$post = $stmt->fetch();
if (!$post) { flash('Post not found.', 'is-danger'); header('Location: admin_blog.php'); exit; }

// If confirmed via POST, delete and redirect back to admin list
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // EXAMPLE: PREPARED_DELETE — Delete with a prepared statement
  $del = $pdo->prepare("DELETE FROM posts WHERE id = ?");
  $del->execute([$post_id]);
  // EXAMPLE: PRG — Flash and redirect after mutating
  flash('Post deleted.', 'is-success');
  header('Location: admin_blog.php'); exit;
}

$pageTitle = 'Delete Post - ' . ($siteName ?? 'Site');
?>
<?php require __DIR__ . '/templates/head.php'; ?>
<?php require __DIR__ . '/templates/nav.php'; ?>
<?php require __DIR__ . '/templates/flash.php'; ?>

<section class="section">
  <div class="container">
    <h1 class="title">Delete “<?= htmlspecialchars($post['title']) ?>”?</h1>
    <p class="mb-4">Created: <?= htmlspecialchars(date('M j, Y g:ia', strtotime($post['created_at']))) ?></p>

    <form method="post" class="box">
      <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
      <div class="field is-grouped">
        <div class="control"><button class="button is-danger" type="submit">Yes, delete</button></div>
        <div class="control"><a class="button" href="admin_blog.php">Cancel</a></div>
      </div>
    </form>
  </div>
</section>

<?php require __DIR__ . '/templates/footer.php'; ?>

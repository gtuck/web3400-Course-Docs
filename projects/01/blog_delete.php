<?php
// filepath: blog_delete.php
require __DIR__ . '/config.php';

$post_id = (int)($_GET['post_id'] ?? ($_POST['post_id'] ?? 0));
if ($post_id <= 0) { flash('Invalid post id.', 'danger'); header('Location: admin_blog.php'); exit; }

$stmt = $pdo->prepare("SELECT id, title, created_at FROM posts WHERE id = ? LIMIT 1");
$stmt->execute([$post_id]);
$post = $stmt->fetch();
if (!$post) { flash('Post not found.', 'danger'); header('Location: admin_blog.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $del = $pdo->prepare("DELETE FROM posts WHERE id = ?");
  $del->execute([$post_id]);
  flash('Post deleted.');
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

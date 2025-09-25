<?php
// filepath: blog_post.php
require __DIR__ . '/config.php';

$post_id = (int)($_GET['post_id'] ?? 0);
if ($post_id <= 0) {
  flash('Invalid post id.', 'danger');
  header('Location: index.php');
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? LIMIT 1");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

$pageTitle = ($post ? $post['title'] : 'Post Not Found') . ' - ' . ($siteName ?? 'Site');
?>
<?php require __DIR__ . '/templates/head.php'; ?>
<?php require __DIR__ . '/templates/nav.php'; ?>
<?php require __DIR__ . '/templates/flash.php'; ?>

<section class="section">
  <div class="container">
    <?php if (!$post): ?>
      <h1 class="title">Post not found</h1>
      <p>Sorry, that post does not exist.</p>
    <?php else: ?>
      <h1 class="title"><?= htmlspecialchars($post['title']) ?></h1>
      <p class="is-size-7 has-text-grey">
        Created <?= htmlspecialchars(date('M j, Y g:ia', strtotime($post['created_at']))) ?>
        <?php if ($post['updated_at']): ?> · Updated <?= htmlspecialchars(date('M j, Y g:ia', strtotime($post['updated_at']))) ?><?php endif; ?>
      </p>
      <div class="content"><?= nl2br(htmlspecialchars($post['body'])) ?></div>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/templates/footer.php'; ?>

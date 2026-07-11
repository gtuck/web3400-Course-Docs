<?php
/*
  Home Page (Public list of posts)
  - Reads the latest posts and shows title, date, and excerpt
  - Titles link to the single post view via `blog_post.php?post_id=ID`
*/

require __DIR__ . '/config.php';
$pageTitle = 'Home - ' . ($siteName ?? 'Site');

// Simple read-only query for the 10 newest posts
$stmt = $pdo->query("SELECT id, title, slug, body, created_at FROM posts ORDER BY created_at DESC LIMIT 10");
$posts = $stmt->fetchAll();
?>
<?php require __DIR__ . '/templates/head.php'; ?>
<?php require __DIR__ . '/templates/nav.php'; ?>
<?php require __DIR__ . '/templates/flash.php'; ?>

<section class="section">
  <div class="container">
    <h1 class="title">Latest Posts</h1>
    <?php if (!$posts): ?>
      <p>No posts yet. <a href="admin_blog.php">Create one</a>.</p>
    <?php else: ?>
      <div class="content">
        <?php foreach ($posts as $p): ?>
          <article class="box mb-4">
            <h2 class="title is-4">
              <a href="blog_post.php?post_id=<?= (int)$p['id'] ?>">
                <?= htmlspecialchars($p['title']) ?>
              </a>
            </h2>
            <p class="is-size-7 has-text-grey">
              <?= htmlspecialchars(date('M j, Y g:ia', strtotime($p['created_at']))) ?>
            </p>
            <!-- Create a short, safe preview by stripping tags, limiting length, and escaping -->
            <p><?= htmlspecialchars(mb_strimwidth(strip_tags($p['body']), 0, 240, '…')) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/templates/footer.php'; ?>

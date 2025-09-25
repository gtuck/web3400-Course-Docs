<?php
// filepath: admin_blog.php
require __DIR__ . '/config.php';
$pageTitle = 'Blog Admin - ' . ($siteName ?? 'Site');

$stmt = $pdo->query("SELECT id, title, slug, created_at, updated_at FROM posts ORDER BY created_at DESC");
$rows = $stmt->fetchAll();
?>
<?php require __DIR__ . '/templates/head.php'; ?>
<?php require __DIR__ . '/templates/nav.php'; ?>
<?php require __DIR__ . '/templates/flash.php'; ?>

<section class="section">
  <div class="container">
    <h1 class="title">Blog Admin</h1>
    <p class="mb-4">
      <a class="button is-link" href="blog_create.php">Create Post</a>
    </p>

    <div class="table-container">
      <table class="table is-fullwidth is-striped is-hoverable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Created</th>
            <th>Updated</th>
            <th class="has-text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= (int)$r['id'] ?></td>
              <td>
                <a href="blog_post.php?post_id=<?= (int)$r['id'] ?>" target="_blank" rel="noopener">
                  <?= htmlspecialchars($r['title']) ?>
                </a>
              </td>
              <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($r['created_at']))) ?></td>
              <td><?= $r['updated_at'] ? htmlspecialchars(date('Y-m-d H:i', strtotime($r['updated_at']))) : '—' ?></td>
              <td class="has-text-right">
                <a class="button is-small is-info" href="blog_edit.php?post_id=<?= (int)$r['id'] ?>">Edit</a>
                <a class="button is-small is-danger" href="blog_delete.php?post_id=<?= (int)$r['id'] ?>">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$rows): ?>
            <tr><td colspan="5">No posts yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php require __DIR__ . '/templates/footer.php'; ?>

<?php $this->layout('layouts/main'); $this->start('content'); ?>
<section class="section">
  <div class="container">
    <div class="level">
      <div class="level-left">
        <h1 class="title">Posts</h1>
      </div>
      <div class="level-right">
        <a class="button is-primary" href="/admin/posts/create">New Post</a>
      </div>
    </div>

    <div class="table-container">
      <table class="table is-fullwidth is-striped is-hoverable">
        <thead>
          <tr>
            <th>Title</th>
            <th>Status</th>
            <th>Featured</th>
            <th>Published</th>
            <th class="has-text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $p): ?>
            <tr>
              <td>
                <a href="/posts/<?= $this->e($p['slug']) ?>" target="_blank" rel="noopener"><?= $this->e($p['title']) ?></a>
              </td>
              <td><?= $this->e(ucfirst($p['status'])) ?></td>
              <td><?= ((int)($p['is_featured'] ?? 0) === 1) ? 'Yes' : 'No' ?></td>
              <td><?= $this->e($p['published_at'] ?: '—') ?></td>
              <td class="has-text-right">
                <a class="button is-small" href="/admin/posts/<?= $this->e($p['id']) ?>/edit">Edit</a>
                <?php if (($p['status'] ?? '') !== 'published'): ?>
                  <form class="is-inline" method="post" action="/admin/posts/<?= $this->e($p['id']) ?>/publish" style="display:inline">
                    <?php $this->csrfField(); ?>
                    <button class="button is-small is-success" type="submit">Publish</button>
                  </form>
                <?php else: ?>
                  <form class="is-inline" method="post" action="/admin/posts/<?= $this->e($p['id']) ?>/unpublish" style="display:inline">
                    <?php $this->csrfField(); ?>
                    <button class="button is-small is-warning" type="submit">Unpublish</button>
                  </form>
                <?php endif; ?>
                <form class="is-inline" method="post" action="/admin/posts/<?= $this->e($p['id']) ?>/delete" style="display:inline" onsubmit="return confirm('Delete this post?');">
                  <?php $this->csrfField(); ?>
                  <button class="button is-small is-danger" type="submit">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  </section>
<?php $this->end(); ?>


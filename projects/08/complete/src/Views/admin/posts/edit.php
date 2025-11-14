<?php $this->layout('layouts/main'); $this->start('content'); ?>
<section class="section">
  <div class="container">
    <div class="level">
      <div class="level-left">
        <h1 class="title">Edit Post</h1>
      </div>
      <div class="level-right">
        <a class="button" href="/admin/posts">Back</a>
      </div>
    </div>

    <?php $this->insert('admin/posts/_form', ['post' => $post]); ?>

    <div class="box mt-4">
      <h2 class="subtitle">Quick Actions</h2>
      <div class="buttons">
        <?php if (($post['status'] ?? '') !== 'published'): ?>
          <form method="post" action="/admin/posts/<?= $this->e($post['id']) ?>/publish">
            <?php $this->csrfField(); ?>
            <button class="button is-success" type="submit">Publish</button>
          </form>
        <?php else: ?>
          <form method="post" action="/admin/posts/<?= $this->e($post['id']) ?>/unpublish">
            <?php $this->csrfField(); ?>
            <button class="button is-warning" type="submit">Unpublish</button>
          </form>
        <?php endif; ?>
        <form method="post" action="/admin/posts/<?= $this->e($post['id']) ?>/delete" onsubmit="return confirm('Delete this post?');">
          <?php $this->csrfField(); ?>
          <button class="button is-danger" type="submit">Delete</button>
        </form>
      </div>
    </div>
  </div>
</section>
<?php $this->end(); ?>


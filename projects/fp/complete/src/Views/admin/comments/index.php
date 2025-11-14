<?php $this->layout('layouts/main'); $this->start('content'); ?>
<section class="section">
  <div class="container">
    <div class="level">
      <div class="level-left">
        <h1 class="title">Manage Comments</h1>
      </div>
    </div>

    <div class="buttons mb-3">
      <a class="button<?= $status === null ? ' is-link is-light' : '' ?>" href="/admin/comments">All</a>
      <a class="button<?= $status === 'pending' ? ' is-link is-light' : '' ?>" href="/admin/comments?status=pending">Pending</a>
      <a class="button<?= $status === 'published' ? ' is-link is-light' : '' ?>" href="/admin/comments?status=published">Published</a>
      <a class="button<?= $status === 'deleted' ? ' is-link is-light' : '' ?>" href="/admin/comments?status=deleted">Deleted</a>
    </div>

    <?php if (empty($comments)): ?>
      <p class="has-text-grey">No comments found for this filter.</p>
    <?php else: ?>
      <table class="table is-fullwidth is-striped is-hoverable">
        <thead>
          <tr>
            <th>Post</th>
            <th>Author</th>
            <th>Status</th>
            <th>Created</th>
            <th>Excerpt</th>
            <th class="has-text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($comments as $comment): ?>
            <tr>
              <td>
                <a href="/posts/<?= $this->e($comment['post_slug']) ?>" target="_blank">
                  <?= $this->e($comment['post_title']) ?>
                </a>
              </td>
              <td>
                <?= $this->e($comment['user_name'] ?? '') ?><br>
                <span class="is-size-7 has-text-grey"><?= $this->e($comment['user_email'] ?? '') ?></span>
              </td>
              <td><?= $this->e(ucfirst($comment['status'])) ?></td>
              <td><?= $this->e($comment['created_at'] ?? '') ?></td>
              <td>
                <?php
                  $body = (string)($comment['body'] ?? '');
                  $excerpt = mb_substr($body, 0, 80);
                ?>
                <?= $this->e($excerpt) ?><?= mb_strlen($body) > 80 ? '…' : '' ?>
              </td>
              <td class="has-text-right">
                <?php if ($comment['status'] !== 'published'): ?>
                  <form class="is-inline" method="post" action="/admin/comments/<?= (int)$comment['id'] ?>/publish">
                    <?php $this->csrfField(); ?>
                    <button class="button is-small is-success" type="submit">Publish</button>
                  </form>
                <?php endif; ?>
                <?php if ($comment['status'] !== 'deleted'): ?>
                  <form class="is-inline" method="post" action="/admin/comments/<?= (int)$comment['id'] ?>/delete" onsubmit="return confirm('Delete this comment?');">
                    <?php $this->csrfField(); ?>
                    <button class="button is-small is-danger is-light" type="submit">Delete</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</section>
<?php $this->end(); ?>


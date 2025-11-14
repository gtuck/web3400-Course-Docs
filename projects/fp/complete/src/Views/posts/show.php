<?php $this->layout('layouts/main'); $this->start('content'); ?>
<section class="section">
  <div class="container content">
    <p class="is-size-7 has-text-grey">
      <a href="/">← Back</a>
    </p>
    <h1 class="title"><?= $this->e($post['title']) ?></h1>
    <p class="is-size-7 has-text-grey">
      By <?= $this->e($post['author_name'] ?? 'Unknown') ?> • <?= $this->e($published_human ?? '') ?>
    </p>
    <p class="is-size-7 has-text-grey">
      Likes: <?= (int)($post['likes'] ?? 0) ?> •
      Favs: <?= (int)($post['favs'] ?? 0) ?> •
      Comments: <?= (int)($post['comments_count'] ?? 0) ?>
    </p>
    <?php $u = $_SESSION['user_id'] ?? null; $role = $_SESSION['user_role'] ?? 'user'; ?>
    <?php if ($u): ?>
      <div class="buttons mb-4">
        <form method="post" action="/posts/<?= (int)$post['id'] ?>/<?= !empty($post['is_liked_by_user']) ? 'unlike' : 'like' ?>">
          <?php $this->csrfField(); ?>
          <button class="button is-small <?= !empty($post['is_liked_by_user']) ? 'is-link is-light' : '' ?>" type="submit">
            <?= !empty($post['is_liked_by_user']) ? 'Unlike' : 'Like' ?>
          </button>
        </form>
        <form method="post" action="/posts/<?= (int)$post['id'] ?>/<?= !empty($post['is_favorited_by_user']) ? 'unfav' : 'fav' ?>">
          <?php $this->csrfField(); ?>
          <button class="button is-small <?= !empty($post['is_favorited_by_user']) ? 'is-warning is-light' : '' ?>" type="submit">
            <?= !empty($post['is_favorited_by_user']) ? 'Unfav' : 'Fav' ?>
          </button>
        </form>
      </div>
    <?php else: ?>
      <p class="is-size-7 has-text-grey mb-4">
        <a href="/login">Log in</a> to like, favorite, or comment on this post.
      </p>
    <?php endif; ?>
    <figure class="image is-3by1 mb-4">
      <img src="<?= $this->e($post['featured_image'] ?? 'https://picsum.photos/1200/400') ?>" alt="Featured image">
    </figure>
    <div>
      <?= nl2br($this->e($post['body'])) ?>
    </div>
    <hr>

    <h2 class="title is-4">Comments</h2>
    <?php if (empty($comments)): ?>
      <p class="has-text-grey">No comments yet – be the first to comment.</p>
    <?php else: ?>
      <?php foreach ($comments as $comment): ?>
        <article class="box mb-3">
          <p class="is-size-7 has-text-grey">
            <strong><?= $this->e($comment['user_name'] ?? 'User') ?></strong>
            • <?= $this->e($comment['created_human'] ?? '') ?>
          </p>
          <p><?= nl2br($this->e($comment['body'] ?? '')) ?></p>
          <?php if ($u && ((int)$comment['user_id'] === (int)$u || in_array($role, ['admin','editor'], true))): ?>
            <form class="mt-2" method="post" action="/comments/<?= (int)$comment['id'] ?>/delete" onsubmit="return confirm('Delete this comment?');">
              <?php $this->csrfField(); ?>
              <button class="button is-small is-danger is-light" type="submit">Delete</button>
            </form>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($u): ?>
      <h3 class="title is-5 mt-5">Add a Comment</h3>
      <form method="post" action="/posts/<?= $this->e($post['slug']) ?>/comments">
        <?php $this->csrfField(); ?>
        <div class="field">
          <div class="control">
            <textarea class="textarea" name="body" rows="3" required></textarea>
          </div>
        </div>
        <div class="field">
          <div class="control">
            <button class="button is-link" type="submit">Post Comment</button>
          </div>
        </div>
      </form>
    <?php endif; ?>
  </div>
</section>
<?php $this->end(); ?>

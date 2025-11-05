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
    <figure class="image is-3by1 mb-4">
      <img src="<?= $this->e($post['featured_image'] ?? 'https://picsum.photos/1200/400') ?>" alt="Featured image">
    </figure>
    <div>
      <?= nl2br($this->e($post['body'])) ?>
    </div>
  </div>
</section>
<?php $this->end(); ?>


<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>

<section class="hero is-primary">
    <div class="hero-body">
        <h1 class="title">Welcome to <?= $this->e($siteName ?? 'My PHP Site') ?></h1>
        <p class="subtitle">This page is rendered with a vanilla PHP template engine.</p>
    </div>
</section>
<section class="section">
    <h1 class="title">Latest Posts</h1>
    <?php foreach ($posts as $post): ?>
        <article class="box mb-4">
            <h3 class="title is-4">
                <?= $this->e($post['title']) ?>
            </h3>
            <p><?= $this->e($post['body']) ?></p>
        </article>
    <?php endforeach; ?>
</section>
<?php $this->end(); ?>
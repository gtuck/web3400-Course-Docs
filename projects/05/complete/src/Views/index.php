<?php $this->layout('layouts/main'); ?>

<?php $this->start('content'); ?>

<!-- BEGIN HERO -->
<section class="hero is-primary">
    <div class="hero-body">
        <h1 class="title">Welcome to <?= $this->e($siteName ?? 'My PHP Site') ?></h1>
        <p class="subtitle">This page is rendered with a vanilla PHP template engine.</p>
    </div>
</section>
<!-- END HERO -->

<!-- BEGIN PAGE CONTENT -->
<h2>Blog Posts</h2>

<?php foreach ($posts as $post): ?>

    <h2><?= htmlspecialchars($post["title"]) ?></h2>
    <p><?= htmlspecialchars($post["body"]) ?></p>

<?php endforeach; ?>
<!-- END PAGE CONTENT -->

<?php $this->end(); ?>
<?php $this->layout('layouts/main'); ?>

<?php $this->start('content'); ?>

<h1>Welcome to <?= $this->e($siteName ?? 'My PHP Site') ?></h1>
<p>This page is rendered with a vanilla PHP template engine.</p>

<h2>Blog Posts</h2>

<?php foreach ($posts as $post): ?>

    <h2><?= htmlspecialchars($post["title"]) ?></h2>
    <p><?= htmlspecialchars($post["body"]) ?></p>

<?php endforeach; ?>

<?php $this->end(); ?>
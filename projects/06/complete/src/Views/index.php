<?php
/**
 * Home Page View
 *
 * PURPOSE:
 * Displays the home page with a hero section and list of blog posts
 *
 * AVAILABLE VARIABLES:
 * - $siteName: Website name (from shared variables)
 * - $posts: Array of blog post records from the database
 * - $title: Page title (set in controller)
 *
 * LAYOUT: layouts/main
 */
?>
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
    <article class="box">
        <h2><?= $this->e($post["title"]) ?></h2>
        <p><?= $this->e($post["body"]) ?></p>
    </article>
<?php endforeach; ?>
<!-- END PAGE CONTENT -->

<?php $this->end(); ?>
<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>

<section class="hero is-primary">
    <div class="hero-body">
        <h1 class="title">Welcome to <?= $this->e($siteName ?? 'My PHP Site') ?></h1>
        <p class="subtitle">This page is rendered with a vanilla PHP template engine.</p>
    </div>
</section>
<section class="section">
    <h1 class="title">Featured Posts</h1>
    <?php foreach ($posts as $post): ?>
        <article class="box mb-4">
            <div class="media">
                <figure class="media-left">
                    <p class="image is-128x128">
                        <img src="<?= $this->e($post['featured_image'] ?? 'https://picsum.photos/200') ?>" alt="Featured image">
                    </p>
                </figure>
                <div class="media-content">
                    <h3 class="title is-4"><?= $this->e($post['title']) ?></h3>
                    <p class="is-size-7 has-text-grey">
                        By <?= $this->e($post['author_name'] ?? 'Unknown') ?> •
                        <?= $this->e($post['published_human'] ?? '') ?>
                    </p>
                    <p class="mt-2">
                        <?php $body = strip_tags((string)($post['body'] ?? '')); ?>
                        <?= $this->e(mb_substr($body, 0, 100)) ?><?= mb_strlen($body) > 100 ? '…' : '' ?>
                        <a href="/posts/<?= $this->e($post['slug']) ?>">read more</a>
                    </p>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</section>
<?php $this->end(); ?>

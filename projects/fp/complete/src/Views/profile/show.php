<?php $this->layout('layouts/main');
$this->start('content'); ?>
<section class="section">
    <div class="container">
        <h1 class="title">Your Profile</h1>
        <div class="box">
            <p><strong>Name:</strong> <?= $this->e(($user['name'] ?? '') ?: ($this->user()['name'] ?? '')) ?></p>
            <p><strong>Email:</strong> <?= $this->e(($user['email'] ?? '') ?: ($this->user()['email'] ?? '')) ?></p>
        </div>
        <div class="buttons">
            <a class="button is-link" href="/profile/edit">Edit Profile</a>
        </div>

        <hr>
        <style>
            .tabs-content li {
                display: none;
                list-style: none;
            }

            .tabs-content li.is-active {
                display: block;
            }
        </style>
        <h2 class="title is-5">Your Activity</h2>
        <div class="tabs-wrapper">
            <div class="tabs is-boxed">
                <ul>
                    <li class="is-active">
                        <a>Likes</a>
                    </li>
                    <li>
                        <a>Favs</a>
                    </li>
                    <li>
                        <a>Comments</a>
                    </li>
                </ul>
            </div>
            <div class="tabs-content">
                <ul>
                    <li class="is-active">
                        <?php if (empty($likedPosts)): ?>
                            <p class="has-text-grey">You haven't liked any posts yet.</p>
                        <?php else: ?>
                            <?php foreach ($likedPosts as $post): ?>
                                <p>
                                    <a href="/posts<?= $user ? '/' . $this->e($post['slug']) : '' ?>">
                                        <?= $this->e($post['title']) ?>

                                    </a>
                                </p>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if (empty($favoritedPosts)): ?>
                            <p class="has-text-grey">You haven't favorited any posts yet.</p>
                        <?php else: ?>
                            <?php foreach ($favoritedPosts as $post): ?>
                                <p>
                                    <a href="/posts/<?= $this->e($post['slug']) ?>"><?= $this->e($post['title']) ?></a>
                                </p>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if (empty($commentedPosts)): ?>
                            <p class="has-text-grey">You haven't commented on any posts yet.</p>
                        <?php else: ?>
                            <?php foreach ($commentedPosts as $post): ?>
                                <p>
                                    <a href="/posts/<?= $this->e($post['slug']) ?>"><?= $this->e($post['title']) ?></a>
                                </p>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<?php $this->end(); ?>

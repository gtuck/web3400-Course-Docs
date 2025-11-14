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

        <h2 class="title is-5">Your Activity</h2>

        <div class="tabs is-boxed">
            <ul>
                <li class="is-active" data-tab="likes"><a>Likes</a></li>
                <li data-tab="favs"><a>Favs</a></li>
                <li data-tab="comments"><a>Comments</a></li>
            </ul>
        </div>

        <div id="tab-likes" class="profile-tab-panel">
            <?php if (empty($likedPosts)): ?>
                <p class="has-text-grey">You haven’t liked any posts yet.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($likedPosts as $post): ?>
                        <li>
                            <a href="/posts<?= $user ? '/' . $this->e($post['slug']) : '' ?>">
                                <?= $this->e($post['title']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div id="tab-favs" class="profile-tab-panel is-hidden">
            <?php if (empty($favoritedPosts)): ?>
                <p class="has-text-grey">You haven’t favorited any posts yet.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($favoritedPosts as $post): ?>
                        <li><a href="/posts/<?= $this->e($post['slug']) ?>"><?= $this->e($post['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div id="tab-comments" class="profile-tab-panel is-hidden">
            <?php if (empty($commentedPosts)): ?>
                <p class="has-text-grey">You haven’t commented on any posts yet.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($commentedPosts as $post): ?>
                        <li><a href="/posts/<?= $this->e($post['slug']) ?>"><?= $this->e($post['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php $this->end(); ?>

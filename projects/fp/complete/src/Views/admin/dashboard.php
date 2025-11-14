<?php $this->layout('layouts/main'); ?>

<?php $this->start('content'); ?>
<section class="section">
    <h1 class="title">Admin Dashboard</h1>

    <?php $this->insert('partials/flash'); ?>

    <div class="columns is-multiline">
        <div class="column is-4">
            <div class="box">
                <div class="heading">Posts</div>
                <div class="title">Total: <?= $this->e($kpis['total_posts'] ?? 0) ?></div>
                <div class="level">
                    <div class="level-item">
                        <div>
                            <div class="heading">Draft</div>
                            <div class="title is-5"><?= $this->e($kpis['draft_posts'] ?? 0) ?></div>
                        </div>
                    </div>
                    <div class="level-item">
                        <div>
                            <div class="heading">Published</div>
                            <div class="title is-5"><?= $this->e($kpis['published_posts'] ?? 0) ?></div>
                        </div>
                    </div>
                    <div class="level-item">
                        <div>
                            <div class="heading">Featured</div>
                            <div class="title is-5"><?= $this->e($kpis['featured_posts'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="column is-4">
            <div class="box">
                <div class="heading">Users</div>
                <div class="title">Total: <?= $this->e($kpis['total_users'] ?? 0) ?></div>
                <div class="level">
                    <div class="level-item">
                        <div>
                            <div class="heading">Admins</div>
                            <div class="title is-5"><?= $this->e($kpis['total_admins'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                <?php if (!empty($kpis['most_active_user'])): ?>
                    <p class="subtitle is-6 mt-3">
                        Most active: <?= $this->e($kpis['most_active_user']) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="column is-4">
            <div class="box">
                <div class="heading">Contact Messages</div>
                <div class="title">Total: <?= $this->e($kpis['total_contacts'] ?? 0) ?></div>
            </div>
        </div>

        <div class="column is-12">
            <div class="box">
                <div class="heading">Engagement</div>
                <div class="columns">
                    <div class="column">
                        <p class="heading">Avg Likes / Post</p>
                        <p class="title is-5"><?= $this->e($kpis['average_likes_per_post'] ?? 0) ?></p>
                    </div>
                    <div class="column">
                        <p class="heading">Avg Favs / Post</p>
                        <p class="title is-5"><?= $this->e($kpis['average_favs_per_post'] ?? 0) ?></p>
                    </div>
                    <div class="column">
                        <p class="heading">Avg Comments / Post</p>
                        <p class="title is-5"><?= $this->e($kpis['average_comments_per_post'] ?? 0) ?></p>
                    </div>
                    <div class="column">
                        <p class="heading">Total Interactions</p>
                        <p class="title is-5"><?= $this->e($kpis['total_interactions'] ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="columns">
        <div class="column is-6">
            <div class="panel is-success">
                <p class="panel-heading">Recent Contact Messages</p>
                <div class="panel-block">
                    <table class="table is-fullwidth is-striped is-narrow">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recentContacts as $contact): ?>
                            <tr>
                                <td><?= $this->e($contact['name'] ?? '') ?></td>
                                <td><?= $this->e($contact['email'] ?? '') ?></td>
                                <td><?= $this->e(mb_strimwidth($contact['message'] ?? '', 0, 60, '…')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="column is-6">
            <div class="panel is-link">
                <p class="panel-heading">Recent Users & Posts</p>
                <div class="panel-block">
                    <div class="columns is-multiline is-gapless" style="width: 100%;">
                        <div class="column is-12">
                            <h3 class="title is-6">Latest Users</h3>
                            <ul>
                                <?php foreach ($recentUsers as $user): ?>
                                    <li>
                                        <?= $this->e($user['name'] ?? '') ?>
                                        (<?= $this->e($user['email'] ?? '') ?>)
                                        – <?= $this->e($user['role'] ?? '') ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="column is-12 mt-3">
                            <h3 class="title is-6">Latest Posts</h3>
                            <ul>
                                <?php foreach ($recentPosts as $post): ?>
                                    <li>
                                        <?= $this->e($post['title'] ?? '') ?>
                                        (<?= $this->e($post['status'] ?? '') ?>)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $this->end(); ?>

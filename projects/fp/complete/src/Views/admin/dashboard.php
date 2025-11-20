<?php $this->layout('layouts/main'); ?>

<?php $this->start('content'); ?>
<section class="section">
    <div class="container">
        <h1 class="title">Admin Dashboard</h1>
        <p class="subtitle">Welcome to your CMS administration panel</p>

        <?php $this->insert('partials/flash'); ?>

        <!-- KPI Cards -->
        <h2 class="title is-4 mt-5">Key Performance Indicators</h2>
        <div class="columns is-multiline">
            <!-- Posts KPIs -->
            <div class="column is-one-quarter">
                <div class="box has-background-info-light">
                    <p class="heading">Total Posts</p>
                    <p class="title"><?= $this->e($kpis['total_posts']) ?></p>
                </div>
            </div>
            <div class="column is-one-quarter">
                <div class="box has-background-warning-light">
                    <p class="heading">Draft Posts</p>
                    <p class="title"><?= $this->e($kpis['draft_posts']) ?></p>
                </div>
            </div>
            <div class="column is-one-quarter">
                <div class="box has-background-success-light">
                    <p class="heading">Published Posts</p>
                    <p class="title"><?= $this->e($kpis['published_posts']) ?></p>
                </div>
            </div>
            <div class="column is-one-quarter">
                <div class="box has-background-link-light">
                    <p class="heading">Featured Posts</p>
                    <p class="title"><?= $this->e($kpis['featured_posts']) ?></p>
                </div>
            </div>

            <!-- Users KPIs -->
            <div class="column is-one-quarter">
                <div class="box has-background-primary-light">
                    <p class="heading">Total Users</p>
                    <p class="title"><?= $this->e($kpis['total_users']) ?></p>
                </div>
            </div>
            <div class="column is-one-quarter">
                <div class="box has-background-danger-light">
                    <p class="heading">Administrators</p>
                    <p class="title"><?= $this->e($kpis['total_admins']) ?></p>
                </div>
            </div>
            <div class="column is-one-quarter">
                <div class="box has-background-info-light">
                    <p class="heading">Contact Messages</p>
                    <p class="title"><?= $this->e($kpis['total_contacts']) ?></p>
                </div>
            </div>
            <div class="column is-one-quarter">
                <div class="box has-background-warning-light">
                    <p class="heading">Total Interactions</p>
                    <p class="title"><?= $this->e($kpis['total_interactions']) ?></p>
                </div>
            </div>

            <!-- Engagement Averages -->
            <div class="column is-one-third">
                <div class="box has-background-success-light">
                    <p class="heading">Avg Likes per Post</p>
                    <p class="title"><?= $this->e($kpis['average_likes_per_post']) ?></p>
                </div>
            </div>
            <div class="column is-one-third">
                <div class="box has-background-link-light">
                    <p class="heading">Avg Favs per Post</p>
                    <p class="title"><?= $this->e($kpis['average_favs_per_post']) ?></p>
                </div>
            </div>
            <div class="column is-one-third">
                <div class="box has-background-primary-light">
                    <p class="heading">Avg Comments per Post</p>
                    <p class="title"><?= $this->e($kpis['average_comments_per_post']) ?></p>
                </div>
            </div>
        </div>

        <!-- Most Active User -->
        <?php if ($kpis['most_active_user']): ?>
        <div class="notification is-info is-light">
            <strong>Most Active User:</strong> <?= $this->e($kpis['most_active_user']) ?>
        </div>
        <?php endif; ?>

        <!-- Recent Activity Section -->
        <div class="columns mt-5">
            <!-- Recent Contact Messages -->
            <div class="column is-half">
                <div class="box">
                    <h3 class="title is-5">Recent Contact Messages</h3>
                    <?php if (empty($recentContacts)): ?>
                        <p class="has-text-grey">No contact messages yet.</p>
                    <?php else: ?>
                        <table class="table is-fullwidth is-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentContacts as $contact): ?>
                                <tr>
                                    <td><?= $this->e($contact['name']) ?></td>
                                    <td><?= $this->e($contact['email']) ?></td>
                                    <td><?= $this->e($contact['subject'] ?? 'N/A') ?></td>
                                    <td><?= $this->e(date('M j, Y', strtotime($contact['created_at']))) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="column is-half">
                <div class="box">
                    <h3 class="title is-5">Recent Users</h3>
                    <?php if (empty($recentUsers)): ?>
                        <p class="has-text-grey">No users yet.</p>
                    <?php else: ?>
                        <table class="table is-fullwidth is-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td><?= $this->e($user['name']) ?></td>
                                    <td><?= $this->e($user['email']) ?></td>
                                    <td><span class="tag <?= $user['role'] === 'admin' ? 'is-danger' : 'is-info' ?>"><?= $this->e($user['role']) ?></span></td>
                                    <td><?= $this->e(date('M j, Y', strtotime($user['created_at']))) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Posts -->
        <div class="box mt-4">
            <h3 class="title is-5">Recent Posts</h3>
            <?php if (empty($recentPosts)): ?>
                <p class="has-text-grey">No posts yet.</p>
            <?php else: ?>
                <table class="table is-fullwidth is-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Likes</th>
                            <th>Favs</th>
                            <th>Comments</th>
                            <th>Published</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPosts as $post): ?>
                        <tr>
                            <td>
                                <a href="/posts/<?= $this->e($post['slug']) ?>">
                                    <?= $this->e($post['title']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="tag <?= $post['status'] === 'published' ? 'is-success' : 'is-warning' ?>">
                                    <?= $this->e($post['status']) ?>
                                </span>
                            </td>
                            <td><?= $this->e($post['likes'] ?? 0) ?></td>
                            <td><?= $this->e($post['favs'] ?? 0) ?></td>
                            <td><?= $this->e($post['comments_count'] ?? 0) ?></td>
                            <td><?= $post['published_at'] ? $this->e(date('M j, Y', strtotime($post['published_at']))) : 'N/A' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="box mt-5">
            <h3 class="title is-5">Quick Actions</h3>
            <div class="buttons">
                <a href="/admin/posts/create" class="button is-primary">
                    <span class="icon"><i class="fas fa-plus"></i></span>
                    <span>New Post</span>
                </a>
                <a href="/admin/posts" class="button is-info">
                    <span class="icon"><i class="fas fa-edit"></i></span>
                    <span>Manage Posts</span>
                </a>
                <a href="/admin/comments" class="button is-warning">
                    <span class="icon"><i class="fas fa-comments"></i></span>
                    <span>Moderate Comments</span>
                </a>
                <a href="/admin/users" class="button is-link">
                    <span class="icon"><i class="fas fa-users"></i></span>
                    <span>Manage Users</span>
                </a>
            </div>
        </div>
    </div>
</section>
<?php $this->end(); ?>

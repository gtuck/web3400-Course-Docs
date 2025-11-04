<?php $this->layout('layouts/main');
$this->start('content'); ?>
<section class="section">
    <div class="container">
        <div class="level">
            <h1 class="title level-left">Users</h1>
            <div class="level-right"><a class="button is-primary" href="/admin/users/create">New User</a></div>
        </div>
        <table class="table is-fullwidth is-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($users ?? []) as $u): ?>
                    <tr>
                        <td><?= (int)$u['id'] ?></td>
                        <td><?= $this->e($u['name']) ?></td>
                        <td><?= $this->e($u['email']) ?></td>
                        <td><?= $this->e($u['role']) ?></td>
                        <td><?= (int)$u['is_active'] ? 'Yes' : 'No' ?></td>
                        <td class="has-text-right">
                            <a class="button is-small" href="/admin/users/<?= (int)$u['id'] ?>/edit">Edit</a>
                            <form style="display:inline; margin-left:.5rem" method="post" action="/admin/users/<?= (int)$u['id'] ?>/active">
                                <?php $this->csrfField(); ?>
                                <label class="checkbox">
                                    <input type="checkbox" name="is_active" value="1" <?= (int)$u['is_active'] ? 'checked' : '' ?> onchange="this.form.submit()">
                                    Active
                                </label>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php $this->end(); ?>
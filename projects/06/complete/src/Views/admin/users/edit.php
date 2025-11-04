<?php $this->layout('layouts/main');
$this->start('content'); ?>
<section class="section">
    <div class="container">
        <h1 class="title">Edit User</h1>
        <form class="box" method="post" action="/admin/users/<?= (int)($user['id'] ?? 0) ?>">
            <?php $this->csrfField(); ?>
            <div class="field"><label class="label" for="name">Name</label>
                <div class="control"><input class="input" id="name" type="text" name="name" value="<?= $this->e($user['name'] ?? '') ?>" required></div>
            </div>
            <div class="field"><label class="label" for="email">Email</label>
                <div class="control"><input class="input" id="email" type="email" name="email" value="<?= $this->e($user['email'] ?? '') ?>" required></div>
            </div>
            <div class="field"><label class="label" for="role">Role</label>
                <div class="control">
                    <div class="select"><select id="role" name="role">
                            <?php $roles = ['user', 'editor', 'admin'];
                            foreach ($roles as $r): ?>
                                <option value="<?= $r ?>" <?= (($user['role'] ?? 'user') === $r) ? 'selected' : '' ?>><?= $r ?></option>
                            <?php endforeach; ?>
                        </select></div>
                </div>
            </div>
            <div class="field"><label class="checkbox"><input type="checkbox" name="is_active" value="1" <?= ((int)($user['is_active'] ?? 1)) ? 'checked' : '' ?>> Active</label></div>
            <div class="field is-grouped">
                <div class="control"><button class="button is-primary" type="submit">Save</button></div>
                <div class="control"><a class="button" href="/admin/users">Cancel</a></div>
            </div>
        </form>

        <hr>

        <h2 class="title is-5">Update Role Only</h2>
        <form method="post" action="/admin/users/<?= (int)($user['id'] ?? 0) ?>/role">
            <?php $this->csrfField(); ?>
            <div class="field">
                <div class="select"><select name="role">
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r ?>" <?= (($user['role'] ?? 'user') === $r) ? 'selected' : '' ?>><?= $r ?></option>
                        <?php endforeach; ?>
                    </select></div>
            </div>
            <div class="field"><button class="button is-link" type="submit">Update Role</button></div>
        </form>
    </div>
</section>
<?php $this->end(); ?>
<?php $this->layout('layouts/main');
$this->start('content'); ?>
<section class="section">
    <div class="container">
        <h1 class="title">Edit Profile</h1>
        <form class="box" method="post" action="/profile">
            <?php $this->csrfField(); ?>
            <div class="field">
                <label class="label" for="name">Name</label>
                <div class="control">
                    <input class="input" id="name" type="text" name="name" value="<?= $this->e($user['name'] ?? '') ?>" required>
                </div>
            </div>
            <div class="field">
                <label class="label" for="email">Email</label>
                <div class="control">
                    <input class="input" id="email" type="email" name="email" value="<?= $this->e($user['email'] ?? '') ?>" required>
                </div>
            </div>
            <div class="field is-grouped">
                <div class="control"><button class="button is-primary" type="submit">Save</button></div>
                <div class="control"><a class="button" href="/profile">Cancel</a></div>
            </div>
        </form>

        <hr>

        <h2 class="title is-5">Change Password</h2>
        <form class="box" method="post" action="/profile/password">
            <?php $this->csrfField(); ?>
            <div class="field">
                <label class="label" for="current_password">Current Password</label>
                <div class="control">
                    <input class="input" id="current_password" type="password" name="current_password" required>
                </div>
            </div>
            <div class="field">
                <label class="label" for="new_password">New Password</label>
                <div class="control">
                    <input class="input" id="new_password" type="password" name="new_password" required>
                </div>
            </div>
            <div class="field">
                <label class="label" for="new_password_confirm">Confirm New Password</label>
                <div class="control">
                    <input class="input" id="new_password_confirm" type="password" name="new_password_confirm" required>
                </div>
            </div>
            <div class="field"><button class="button is-link" type="submit">Change Password</button></div>
        </form>
    </div>
</section>
<?php $this->end(); ?>
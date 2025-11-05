<?php $this->layout('layouts/main');
$this->start('content'); ?>
<section class="section">
    <div class="container">
        <h1 class="title">Change Password</h1>
        <form class="box" method="post" action="/profile/password">
            <?php $this->csrfField(); ?>
            <div class="field"><label class="label" for="current_password">Current Password</label>
                <div class="control"><input class="input" id="current_password" type="password" name="current_password" required></div>
            </div>
            <div class="field"><label class="label" for="new_password">New Password</label>
                <div class="control"><input class="input" id="new_password" type="password" name="new_password" required></div>
            </div>
            <div class="field"><label class="label" for="new_password_confirm">Confirm New Password</label>
                <div class="control"><input class="input" id="new_password_confirm" type="password" name="new_password_confirm" required></div>
            </div>
            <div class="field"><button class="button is-link" type="submit">Change Password</button></div>
        </form>
    </div>
</section>
<?php $this->end(); ?>
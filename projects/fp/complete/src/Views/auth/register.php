<?php $this->layout('layouts/main');
$this->start('content'); ?>
<section class="section">
    <div class="container">
        <h1 class="title">Register</h1>
        <form class="box" method="post" action="/register">
            <?php $this->csrfField(); ?>
            <div class="field">
                <label class="label" for="name">Name</label>
                <div class="control">
                    <input class="input" id="name" type="text" name="name" value="<?= $this->e($old['name'] ?? '') ?>" required>
                </div>
            </div>
            <div class="field">
                <label class="label" for="email">Email</label>
                <div class="control">
                    <input class="input" id="email" type="email" name="email" value="<?= $this->e($old['email'] ?? '') ?>" required>
                </div>
            </div>
            <div class="field">
                <label class="label" for="password">Password</label>
                <div class="control">
                    <input class="input" id="password" type="password" name="password" required>
                </div>
            </div>
            <div class="field">
                <label class="label" for="password_confirm">Confirm Password</label>
                <div class="control">
                    <input class="input" id="password_confirm" type="password" name="password_confirm" required>
                </div>
            </div>
            <div class="field is-grouped is-justify-content-space-between is-align-items-center">
                <div class="control">
                    <button class="button is-primary" type="submit">Create Account</button>
                </div>
                <div class="control">
                    <a class="button is-text" href="/login">Already have an account?</a>
                </div>
            </div>
        </form>
    </div>
</section>
<?php $this->end(); ?>
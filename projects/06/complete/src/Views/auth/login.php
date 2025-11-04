<?php $this->layout('layouts/main');
$this->start('content'); ?>
<section class="section">
    <div class="container">
        <h1 class="title">Login</h1>
        <form class="box" method="post" action="/login">
            <?php $this->csrfField(); ?>
            <div class="field">
                <label class="label" for="email">Email</label>
                <div class="control">
                    <input class="input" id="email" type="email" name="email" required>
                </div>
            </div>
            <div class="field">
                <label class="label" for="password">Password</label>
                <div class="control">
                    <input class="input" id="password" type="password" name="password" required>
                </div>
            </div>
            <div class="field is-grouped is-justify-content-space-between is-align-items-center">
                <div class="control">
                    <button class="button is-link" type="submit">Login</button>
                </div>
                <div class="control">
                    <a class="button is-text" href="/register">Create account</a>
                </div>
            </div>
        </form>
    </div>
</section>
<?php $this->end(); ?>
<?php $this->layout('layouts/main'); ?>

<?php $this->start('content'); ?>

<!-- BEGIN PAGE CONTENT -->
<h1>Contact Us</h1>

<form class="box" method="post" action="/contact" novalidate>
    <div class="field">
        <label class="label" for="name">Name</label>
        <div class="control">
            <input id="name" name="name" class="input" type="text" required
                value="<?= htmlspecialchars($old['name'] ?? '') ?>" placeholder="Your name">
        </div>
    </div>

    <div class="field">
        <label class="label" for="email">Email</label>
        <div class="control">
            <input id="email" name="email" class="input" type="email" required
                value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="you@example.com">
        </div>
    </div>

    <div class="field">
        <label class="label" for="message">Message</label>
        <div class="control">
            <textarea id="message" name="message" class="textarea" required
                placeholder="How can we help?"><?= htmlspecialchars($old['message'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="field is-grouped">
        <div class="control">
            <button type="submit" class="button is-primary">
                <span class="icon"><i class="fas fa-paper-plane" aria-hidden="true"></i></span>
                <span>Send</span>
            </button>
        </div>
        <div class="control">
            <a class="button is-light" href="/">Cancel</a>
        </div>
    </div>
</form>
<!-- END PAGE CONTENT -->

<?php $this->end(); ?>
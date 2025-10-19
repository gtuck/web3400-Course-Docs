<?php
/**
 * Contact Form View
 *
 * PURPOSE:
 * Displays a contact form for users to send messages
 *
 * AVAILABLE VARIABLES:
 * - $title: Page title (set in controller)
 * - $old: Array with previous form input (for repopulation after validation errors)
 *         ['name' => string, 'email' => string, 'message' => string]
 * - $csrfToken: CSRF protection token (automatically shared from Controller)
 *
 * SECURITY FEATURES:
 * - CSRF token protection against cross-site request forgery
 * - XSS protection via $this->e() for all user input
 * - Server-side validation (complemented by HTML5 client-side validation)
 *
 * LAYOUT: layouts/main
 */
?>
<?php $this->layout('layouts/main'); ?>

<?php $this->start('content'); ?>

<!-- BEGIN PAGE CONTENT -->
<h1>Contact Us</h1>

<form class="box" method="post" action="/contact" novalidate>
    <!-- CSRF Protection Token (hidden field) -->
    <input type="hidden" name="csrf_token" value="<?= $this->e($csrfToken) ?>">

    <div class="field">
        <label class="label" for="name">Name</label>
        <div class="control">
            <input id="name" name="name" class="input" type="text" required
                value="<?= $this->e($old['name'] ?? '') ?>" placeholder="Your name">
        </div>
    </div>

    <div class="field">
        <label class="label" for="email">Email</label>
        <div class="control">
            <input id="email" name="email" class="input" type="email" required
                value="<?= $this->e($old['email'] ?? '') ?>" placeholder="you@example.com">
        </div>
    </div>

    <div class="field">
        <label class="label" for="message">Message</label>
        <div class="control">
            <textarea id="message" name="message" class="textarea" required
                placeholder="How can we help?"><?= $this->e($old['message'] ?? '') ?></textarea>
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
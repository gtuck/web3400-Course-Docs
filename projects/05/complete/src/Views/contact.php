<?php $this->layout('layouts/main'); ?>

<?php $this->start('content'); ?>

<h1>Contact Us</h1>

<?php if (!empty($errors)): ?>
    <div>
        <ul>
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (!empty($status)): ?>
    <p><?= htmlspecialchars($status) ?></p>
<?php endif; ?>

<form method="post" action="/contact" novalidate>
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

<?php $this->end(); ?>
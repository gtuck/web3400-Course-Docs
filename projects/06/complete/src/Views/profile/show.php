<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>

<section class="section">
  <div class="container">
    <h1 class="title">Your Profile</h1>
    <div class="box">
      <p><strong>Name:</strong> <?= $this->e(($user['name'] ?? $currentUser['name'] ?? '')) ?></p>
      <p><strong>Email:</strong> <?= $this->e(($user['email'] ?? $currentUser['email'] ?? '')) ?></p>
      <p><strong>Role:</strong> <?= $this->e(($user['role'] ?? $userRole ?? 'user')) ?></p>
      <div class="buttons mt-4">
        <a class="button is-link" href="/profile/edit">Edit Profile</a>
      </div>
    </div>

    <h2 class="subtitle">Change Password</h2>
    <form method="post" action="/profile/password" class="box" novalidate>
      <?php $this->csrfField(); ?>
      <div class="field">
        <label class="label" for="current_password">Current Password</label>
        <div class="control"><input class="input" type="password" id="current_password" name="current_password" required></div>
      </div>
      <div class="field">
        <label class="label" for="password">New Password</label>
        <div class="control"><input class="input" type="password" id="password" name="password" required></div>
      </div>
      <div class="field">
        <label class="label" for="password_confirm">Confirm New Password</label>
        <div class="control"><input class="input" type="password" id="password_confirm" name="password_confirm" required></div>
      </div>
      <div class="field"><button class="button is-primary" type="submit">Update Password</button></div>
    </form>
  </div>
</section>

<?php $this->end(); ?>

<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>

<section class="section">
  <div class="container">
    <h1 class="title">Edit Profile</h1>
    <form method="post" action="/profile" class="box" novalidate>
      <?php $this->csrfField(); ?>
      <div class="field">
        <label class="label" for="name">Name</label>
        <div class="control">
          <input class="input" type="text" id="name" name="name" value="<?= $this->e(($user['name'] ?? $currentUser['name'] ?? '')) ?>" required>
        </div>
      </div>
      <div class="field">
        <label class="label" for="email">Email</label>
        <div class="control">
          <input class="input" type="email" id="email" name="email" value="<?= $this->e(($user['email'] ?? $currentUser['email'] ?? '')) ?>" required>
        </div>
      </div>
      <div class="field">
        <button class="button is-primary" type="submit">Save</button>
        <a class="button" href="/profile">Cancel</a>
      </div>
    </form>
  </div>
</section>

<?php $this->end(); ?>


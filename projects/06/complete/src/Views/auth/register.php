<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>

<section class="section">
  <div class="container">
    <h1 class="title">Register</h1>
    <form method="post" action="/register" class="box" novalidate>
      <?php $this->csrfField(); ?>
      <div class="field">
        <label class="label" for="name">Name</label>
        <div class="control"><input class="input" type="text" id="name" name="name" required></div>
      </div>
      <div class="field">
        <label class="label" for="email">Email</label>
        <div class="control"><input class="input" type="email" id="email" name="email" required></div>
      </div>
      <div class="field">
        <label class="label" for="password">Password</label>
        <div class="control"><input class="input" type="password" id="password" name="password" required></div>
      </div>
      <div class="field">
        <label class="label" for="password_confirm">Confirm Password</label>
        <div class="control"><input class="input" type="password" id="password_confirm" name="password_confirm" required></div>
      </div>
      <div class="field">
        <div class="control">
          <button type="submit" class="button is-primary">Create Account</button>
          <a href="/login" class="button is-text">Already have an account?</a>
        </div>
      </div>
    </form>
  </div>
</section>

<?php $this->end(); ?>


<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>

<section class="section">
  <div class="container">
    <h1 class="title">Login</h1>
    <form method="post" action="/login" class="box" novalidate>
      <?php $this->csrfField(); ?>
      <div class="field">
        <label class="label" for="email">Email</label>
        <div class="control"><input class="input" type="email" id="email" name="email" required></div>
      </div>
      <div class="field">
        <label class="label" for="password">Password</label>
        <div class="control"><input class="input" type="password" id="password" name="password" required></div>
      </div>
      <div class="field">
        <div class="control">
          <button type="submit" class="button is-primary">Login</button>
          <a href="/register" class="button is-text">Create an account</a>
        </div>
      </div>
    </form>
  </div>
  </section>

<?php $this->end(); ?>


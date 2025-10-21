<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>

<section class="section">
  <div class="container">
    <h1 class="title">Create User</h1>
    <form class="box" method="post" action="/admin/users" novalidate>
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
        <label class="label" for="role">Role</label>
        <div class="control">
          <div class="select">
            <select id="role" name="role" required>
              <option value="user">user</option>
              <option value="editor">editor</option>
              <option value="admin">admin</option>
            </select>
          </div>
        </div>
      </div>
      <div class="field">
        <label class="label" for="password">Password</label>
        <div class="control"><input class="input" type="password" id="password" name="password" required></div>
      </div>
      <div class="field">
        <button type="submit" class="button is-primary">Create</button>
        <a href="/admin/users" class="button">Cancel</a>
      </div>
    </form>
  </div>
</section>

<?php $this->end(); ?>


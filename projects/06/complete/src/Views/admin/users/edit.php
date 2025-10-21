<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>

<section class="section">
  <div class="container">
    <h1 class="title">Edit User</h1>
    <form class="box" method="post" action="/admin/users/update" novalidate>
      <?php $this->csrfField(); ?>
      <input type="hidden" name="id" value="<?= (int)($user['id'] ?? 0) ?>">
      <div class="field">
        <label class="label" for="name">Name</label>
        <div class="control"><input class="input" type="text" id="name" name="name" value="<?= $this->e($user['name'] ?? '') ?>" required></div>
      </div>
      <div class="field">
        <label class="label" for="email">Email</label>
        <div class="control"><input class="input" type="email" id="email" name="email" value="<?= $this->e($user['email'] ?? '') ?>" required></div>
      </div>
      <div class="field">
        <label class="label" for="role">Role</label>
        <div class="control">
          <div class="select">
            <select id="role" name="role" required>
              <?php $role = $user['role'] ?? 'user'; ?>
              <option value="user" <?= $role==='user'?'selected':'' ?>>user</option>
              <option value="editor" <?= $role==='editor'?'selected':'' ?>>editor</option>
              <option value="admin" <?= $role==='admin'?'selected':'' ?>>admin</option>
            </select>
          </div>
        </div>
      </div>
      <div class="field">
        <label class="checkbox">
          <input type="checkbox" name="active" value="1" <?= ((int)($user['active'] ?? 0) === 1) ? 'checked' : '' ?>> Active
        </label>
      </div>
      <div class="field">
        <button type="submit" class="button is-primary">Save</button>
        <a href="/admin/users" class="button">Cancel</a>
      </div>
    </form>
  </div>
</section>

<?php $this->end(); ?>


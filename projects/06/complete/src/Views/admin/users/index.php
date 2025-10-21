<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>

<section class="section">
  <div class="container">
    <h1 class="title">Users</h1>
    <div class="mb-4">
      <a class="button is-primary" href="/admin/users/create">New User</a>
    </div>
    <div class="table-container">
      <table class="table is-fullwidth is-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Active</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (($users ?? []) as $u): ?>
            <tr>
              <td><?= (int)$u['id'] ?></td>
              <td><?= $this->e($u['name']) ?></td>
              <td><?= $this->e($u['email']) ?></td>
              <td><?= $this->e($u['role']) ?></td>
              <td><?= ((int)$u['active'] === 1) ? 'Yes' : 'No' ?></td>
              <td>
                <a class="button is-small" href="/admin/users/edit?id=<?= (int)$u['id'] ?>">Edit</a>
                <?php if ((int)$u['active'] === 1): ?>
                <form method="post" action="/admin/users/delete" style="display:inline">
                  <?php $this->csrfField(); ?>
                  <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                  <button type="submit" class="button is-small is-danger">Deactivate</button>
                </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php $this->end(); ?>


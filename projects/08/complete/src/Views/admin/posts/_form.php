<?php
// $post optional
$isEdit = isset($post);
$action = $isEdit ? "/admin/posts/{$this->e($post['id'])}" : '/admin/posts';
?>
<form method="post" action="<?= $action ?>">
  <?php $this->csrfField(); ?>
  <div class="field">
    <label class="label" for="title">Title</label>
    <div class="control">
      <input class="input" type="text" id="title" name="title" value="<?= $this->e($post['title'] ?? ($old['title'] ?? '')) ?>" required>
    </div>
  </div>

  <div class="field">
    <label class="label" for="slug">Slug</label>
    <div class="control">
      <input class="input" type="text" id="slug" name="slug" value="<?= $this->e($post['slug'] ?? ($old['slug'] ?? '')) ?>" placeholder="auto from title if blank">
    </div>
    <p class="help">Unique URL segment, e.g. getting-started-with-php</p>
  </div>

  <div class="field">
    <label class="label" for="excerpt">Excerpt</label>
    <div class="control">
      <input class="input" type="text" id="excerpt" name="excerpt" value="<?= $this->e($post['excerpt'] ?? ($old['excerpt'] ?? '')) ?>">
    </div>
  </div>

  <div class="field">
    <label class="label" for="featured_image">Featured Image URL</label>
    <div class="control">
      <input class="input" type="url" id="featured_image" name="featured_image" value="<?= $this->e($post['featured_image'] ?? ($old['featured_image'] ?? 'https://picsum.photos/200')) ?>">
    </div>
  </div>

  <div class="field">
    <label class="label" for="body">Body</label>
    <div class="control">
      <textarea class="textarea" id="body" name="body" rows="10" required><?= $this->e($post['body'] ?? ($old['body'] ?? '')) ?></textarea>
    </div>
  </div>

  <div class="field is-grouped">
    <div class="control">
      <label class="checkbox">
        <input type="checkbox" name="is_featured" value="1" <?= ((int)($post['is_featured'] ?? ($old['is_featured'] ?? 0)) === 1) ? 'checked' : '' ?>>
        Featured on home page
      </label>
    </div>
    <div class="control">
      <div class="select">
        <select name="status">
          <?php $status = $post['status'] ?? ($old['status'] ?? 'draft'); ?>
          <?php foreach (['draft','published','archived','deleted'] as $s): ?>
            <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>

  <div class="field is-grouped">
    <div class="control">
      <button class="button is-primary" type="submit">Save</button>
    </div>
    <div class="control">
      <a class="button is-light" href="/admin/posts">Cancel</a>
    </div>
  </div>
</form>


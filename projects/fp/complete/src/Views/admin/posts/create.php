<?php $this->layout('layouts/main'); $this->start('content'); ?>
<section class="section">
  <div class="container">
    <h1 class="title">Create Post</h1>
    <?php $this->insert('admin/posts/_form'); ?>
  </div>
</section>
<?php $this->end(); ?>


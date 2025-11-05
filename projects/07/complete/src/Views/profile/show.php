<?php $this->layout('layouts/main');
$this->start('content'); ?>
<section class="section">
    <div class="container">
        <h1 class="title">Your Profile</h1>
        <div class="box">
            <p><strong>Name:</strong> <?= $this->e(($user['name'] ?? '') ?: ($this->user()['name'] ?? '')) ?></p>
            <p><strong>Email:</strong> <?= $this->e(($user['email'] ?? '') ?: ($this->user()['email'] ?? '')) ?></p>
        </div>
        <div class="buttons">
            <a class="button is-link" href="/profile/edit">Edit Profile</a>
        </div>
    </div>
</section>
<?php $this->end(); ?>
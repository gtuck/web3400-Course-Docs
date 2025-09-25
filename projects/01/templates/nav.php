<?php
// filepath: templates/nav.php (excerpt showing Admin menu)
?>
<nav class="navbar is-primary" role="navigation" aria-label="main navigation">
  <div class="navbar-brand">
    <a class="navbar-item" href="/"><?= htmlspecialchars($siteName ?? 'Site') ?></a>
    <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navMain">
      <span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span>
    </a>
  </div>

  <div id="navMain" class="navbar-menu">
    <div class="navbar-start">
      <a class="navbar-item" href="/">Home</a>

      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link">Admin</a>
        <div class="navbar-dropdown">
          <a class="navbar-item" href="/admin_blog.php">Blog Admin</a>
        </div>
      </div>
    </div>
  </div>
</nav>
<script>
document.querySelectorAll('.navbar-burger').forEach(b => {
  b.addEventListener('click', () => {
    const target = document.getElementById(b.dataset.target);
    b.classList.toggle('is-active');
    target.classList.toggle('is-active');
  });
});
</script>

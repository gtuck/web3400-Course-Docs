<?php
// filepath: projects/00/templates/nav.php
?>
<!-- BEGIN PAGE HEADER -->
<header class="container">

  <!-- BEGIN MAIN NAV -->
  <nav class="navbar is-fixed-top is-spaced has-shadow is-light" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
      <a class="navbar-item" href="index.php">
        <span class="icon-text">
          <span class="icon"><i class="fas fa-code"></i></span>
          <span><?= htmlspecialchars($siteName ?? 'My PHP Site', ENT_QUOTES) ?></span>
        </span>
      </a>
      <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarMenu">
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
      </a>
    </div>
    <div id="navbarMenu" class="navbar-menu">
      <div class="navbar-start">
        <!-- Admin menu will go here in the future -->
      </div>
      <div class="navbar-end">
        <a class="navbar-item" href="contact.php">Contact</a>
      </div>
    </div>
  </nav>
  <!-- END MAIN NAV -->
  <section class="block">&nbsp;</section>
</header>
<!-- END PAGE HEADER -->

<!-- BEGIN MAIN PAGE CONTENT -->
<main class="container">
<?php $u = $_SESSION['user_id'] ?? null;
$role = $_SESSION['user_role'] ?? 'user'; ?>
<header class="container">
    <nav class="navbar is-fixed-top is-spaced has-shadow" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="/">
                <span class="icon-text">
                    <span class="icon"><i class="fas fa-2x fa-code" aria-hidden="true"></i></span>
                    <span>&nbsp;&nbsp;&nbsp;<?= $this->e($title ?? ($siteName ?? 'Site')) ?></span>
                </span>
            </a>
            <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarMenu">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>
        <div class="navbar-menu is-active">
            <div class="navbar-start">
                <?php if ($u): ?>
                    <a class="navbar-item" href="/profile">Profile</a>
                    <?php if (in_array($role, ['admin','editor'], true)): ?>
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">Admin</a>
                            <div class="navbar-dropdown">
                                <?php if ($role === 'admin'): ?>
                                  <a class="navbar-item" href="/admin/users">Manage Users</a>
                                <?php endif; ?>
                                <a class="navbar-item" href="/admin/posts">Manage Posts</a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="navbar-end">
                <?php if ($u): ?>
                    <div class="navbar-item">
                        <form method="post" action="/logout">
                            <?php $this->csrfField(); ?>
                            <button class="button is-light" type="submit">Logout</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="navbar-item">
                        <div class="buttons">
                            <a class="button is-light" href="/login">Login</a>
                            <a class="button is-primary" href="/register"><strong>Register</strong></a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

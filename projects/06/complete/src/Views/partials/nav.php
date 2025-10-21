<header class="container">
    <nav class="navbar is-fixed-top is-spaced has-shadow" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="/">
                <span class="icon-text">
                    <span class="icon"><i class="fas fa-2x fa-code" aria-hidden="true"></i></span>
                    <span>&nbsp;&nbsp;<?= $this->e($title ?? ($siteName ?? 'Site')) ?></span>
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
            </div>
            <div class="navbar-end">
                <a class="navbar-item" href="/contact">Contact</a>
                <?php if (!($isAuthenticated ?? false)): ?>
                    <a class="navbar-item" href="/register">Register</a>
                    <a class="navbar-item" href="/login">Login</a>
                <?php else: ?>
                    <?php if (($userRole ?? '') === 'admin'): ?>
                        <a class="navbar-item" href="/admin/users">Admin</a>
                    <?php endif; ?>
                    <a class="navbar-item" href="/profile">Profile</a>
                    <form class="navbar-item" method="post" action="/logout" style="display:inline;">
                        <?php $this->csrfField(); ?>
                        <button class="button is-light" type="submit">Logout</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

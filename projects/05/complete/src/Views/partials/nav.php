<header class="container">
    <nav class="navbar is-fixed-top is-spaced has-shadow" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="/">
                <span class="icon-text">
                    <span class="icon"><i class="fas fa-code" aria-hidden="true"></i></span>
                    <span><?= $this->e($title ?? ($siteName ?? 'Site')) ?></span>
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
            </div>
        </div>
    </nav>
</header>
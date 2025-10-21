<!doctype html>
<html lang="en">

<head>
    <?php $this->insert('partials/head', ['title' => ($title ?? 'Home') . ' - ' . ($siteName ?? 'Site')]); ?>
</head>

<body class="has-navbar-fixed-top">
    <?php $this->insert('partials/nav'); ?>
    <?php $this->insert('partials/flash'); ?>

    <main class="container">
        <section class="section content">
            <?php $this->section('content'); ?>
        </section>
    </main>

    <?php $this->insert('partials/footer'); ?>
</body>

</html>
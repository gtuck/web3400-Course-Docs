<?php $pageTitle = $pageTitle ?? ($title ?? 'Site Title'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Project 02 - Micro MVC">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></title>

  <!-- Bulma & Assets (match Project 01) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
  <script src="https://cdn.jsdelivr.net/npm/@vizuaalog/bulmajs@0.12/dist/bulma.min.js" defer></script>
</head>
<body class="has-navbar-fixed-top">
  <main class="container">
    <?= $content ?? '' ?>
  </main>
  <footer class="footer">
    <div class="content has-text-centered">
      <p>&copy; <?= date('Y') ?> — <?= htmlspecialchars(($siteName ?? 'My PHP Site'), ENT_QUOTES) ?></p>
    </div>
  </footer>
</body>
</html>


<?php // Basic head partial; you can add Bulma/FA/CDN links as needed 
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $this->e($title ?? ($siteName ?? 'Site')) ?></title>

<link rel="preconnect" href="https://cdn.jsdelivr.net">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
<script src="https://cdn.jsdelivr.net/npm/@vizuaalog/bulmajs@0.12/dist/bulma.min.js" defer></script>
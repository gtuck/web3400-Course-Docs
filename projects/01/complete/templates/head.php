<?php
// Template: <head> and opening <body>
// - Sets the page <title> using $pageTitle if provided by the page
// - Loads Bulma, FontAwesome, and BulmaJS for UI styling and flash dismissal
$pageTitle = $pageTitle ?? ($siteName ?? 'Site Title');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Project 00 - Template system, PDO, contact form">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></title>

  <!-- Bulma & Assets (match A04 versions) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
  <script src="https://cdn.jsdelivr.net/npm/@vizuaalog/bulmajs@0.12/dist/bulma.min.js" defer></script>
</head>
<body class="has-navbar-fixed-top">

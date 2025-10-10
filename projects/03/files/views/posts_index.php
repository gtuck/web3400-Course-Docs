<!DOCTYPE html>
<html>
<head>
    <title>Posts</title>
    <meta charset="UTF-8">
</head>
<body>

<h1>Posts</h1>

<?php foreach ($posts as $post): ?>

    <h2><?= htmlspecialchars($post["title"]) ?></h2>
    <p><?= htmlspecialchars($post["body"]) ?></p>

<?php endforeach; ?>

</body>
</html>

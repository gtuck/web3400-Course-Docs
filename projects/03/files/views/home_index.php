<!DOCTYPE html>
<html>
<head>
    <title>Posts</title>
</head>
<body>

    <h1>Posts</h1>

    <?php foreach ($posts as $post) : ?>
        <article>
            <h2><?= htmlspecialchars($post['title']) ?></h2>
            <p><?= htmlspecialchars($post['body']) ?></p>
        </article>

    <?php endforeach; ?>

</body>
</html>

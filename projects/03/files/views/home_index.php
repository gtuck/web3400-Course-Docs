<!DOCTYPE html>
<html>
<head>
    <title>Articles</title>
</head>
<body>
    
    <h1>Articles</h1>

    <?php foreach ($articles as $article) : ?>
        <article>
            <h2><?= htmlspecialchars($article['title']) ?></h2>
            <p><?= htmlspecialchars($article['content']) ?></p>
        </article>
        
    <?php endforeach; ?>

</body>
</html>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>Contact Us</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>

<body>
    <h1>Contact Us</h1>

    <?php if (!empty($errors)): ?>
        <div>
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($status)): ?>
        <p><?= htmlspecialchars($status) ?></p>
    <?php endif; ?>

    <form method="post" action="/contact">
        <div>
            <label for="name">Name</label>
            <input id="name" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required />
        </div>
        <div>
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required />
        </div>
        <div>
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" required><?= htmlspecialchars($old['message'] ?? '') ?></textarea>
        </div>
        <button type="submit">Send Message</button>
    </form>
</body>

</html>
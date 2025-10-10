<!DOCTYPE html>
<html>
<head>
    <title>Contacts</title>
    <meta charset="UTF-8">
</head>
<body>

<h1>Contacts</h1>

<?php foreach ($contacts as $contact): ?>

    <h2><?= htmlspecialchars($contact["name"]) ?></h2>
    <p><strong>Email:</strong> <?= htmlspecialchars($contact["email"]) ?></p>
    <p><?= htmlspecialchars($contact["message"]) ?></p>

<?php endforeach; ?>

</body>
</html>

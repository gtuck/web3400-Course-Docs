<?php
if (!empty($_SESSION['messages'])):
?>
    <section class="section">
        <?php foreach ($_SESSION['messages'] as $m): ?>
            <div class="notification <?= htmlspecialchars($m['type'], ENT_QUOTES) ?>">
                <button class="delete" data-bulma="notification"></button>
                <?= htmlspecialchars($m['text'], ENT_QUOTES) ?>
            </div>
        <?php endforeach;
        $_SESSION['messages'] = []; ?>
    </section>
<?php endif; ?>
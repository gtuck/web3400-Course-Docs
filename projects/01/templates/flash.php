<?php
// filepath: templates/flash.php
$flashes = function_exists('get_flashes') ? get_flashes() : [];
if (!empty($flashes)): ?>
  <div class="container my-4">
    <?php foreach ($flashes as $f): ?>
      <div class="notification is-<?= htmlspecialchars($f['type']) ?>">
        <?= htmlspecialchars($f['msg']) ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

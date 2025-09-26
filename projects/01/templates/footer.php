<?php
// Template: Footer + closing tags
// - Closes main container and renders a simple footer
$year = date('Y');
?>
  </main>
  <!-- END MAIN PAGE CONTENT -->

  <!-- BEGIN PAGE FOOTER -->
  <footer class="footer">
    <div class="content has-text-centered">
      <p>&copy; <?= $year ?> - <?= htmlspecialchars($siteName ?? 'My PHP Site', ENT_QUOTES) ?>.</p>
    </div>
  </footer>
  <!-- END PAGE FOOTER -->
</body>
</html>

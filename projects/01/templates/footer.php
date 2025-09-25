<?php
// filepath: projects/00/templates/footer.php
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
<?php
/*
  Contact Page (carried from Project 00)
  - Demonstrates a secure form workflow using PRG (Post → Redirect → Get)
  - Validates inputs server-side and persists messages to the `contact_us` table
  - Uses PDO prepared statements and session-based flash messages
*/

// Load shared configuration: site variables, session/flash, and PDO
require __DIR__ . '/config.php';

// Used in <title> via templates/head.php
$pageTitle = 'Contact - ' . ($siteName ?? 'Site');

// Handle POST submission first (PRG pattern)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Trim and normalize user inputs from the form
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Basic validation rules
    $errors = [];
    if ($name === '' || mb_strlen($name) > 255) $errors[] = 'Please provide your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please provide a valid email.';
    if ($message === '') $errors[] = 'Message is required.';

    // If valid, insert and redirect with success; otherwise redirect with warnings
    if (empty($errors)) {
        // Use a prepared statement to avoid SQL injection
        $stmt = $pdo->prepare('INSERT INTO contact_us (name, email, message) VALUES (:name, :email, :message)');
        $stmt->execute([':name' => $name, ':email' => $email, ':message' => $message]);

        // Bulma notification types use the `is-*` convention (e.g., is-success)
        flash('Thank you for contacting us!', 'is-success');
        header('Location: contact.php');
        exit;
    } else {
        foreach ($errors as $err) flash($err, 'is-warning');
        header('Location: contact.php');
        exit;
    }
}
?>
<?php require __DIR__ . '/templates/head.php'; ?>
<?php require __DIR__ . '/templates/nav.php'; ?>
<?php require __DIR__ . '/templates/flash.php'; ?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
  <h1 class="title">Contact Us</h1>
  <!-- The form uses POST and submits back to this page -->
  <form class="box" action="contact.php" method="post" novalidate>
    <div class="field">
      <label class="label">Your Name</label>
      <div class="control">
        <input class="input" type="text" name="name" value="" required>
      </div>
    </div>
    <div class="field">
      <label class="label">Your Email</label>
      <div class="control">
        <input class="input" type="email" name="email" value="" required>
      </div>
    </div>
    <div class="field">
      <label class="label">Your Message</label>
      <div class="control">
        <textarea class="textarea" name="message" required></textarea>
      </div>
    </div>
    <div class="field">
      <div class="control">
        <button class="button is-primary">Send Message</button>
      </div>
    </div>
  </form>
  <p class="is-size-7 has-text-grey">Email: <?= htmlspecialchars($contactEmail, ENT_QUOTES) ?> | Phone: <?= htmlspecialchars($contactPhone, ENT_QUOTES) ?></p>
</section>
<!-- END YOUR CONTENT -->

<?php require __DIR__ . '/templates/footer.php'; ?>

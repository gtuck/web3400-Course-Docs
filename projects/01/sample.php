<?php
include 'config.php';

// Check if the form was submited
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract, sanitize user input, and assign data to variables
    $username = $_POST['username'];
    $email = $_POST['email'];
    $securityQuestion = $_POST['security_question'];
    $securityAnswer = $_POST['security_answer'];

    // Prepare SQL to validate user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND email = ? AND security_question = ? AND security_question_answer = ?");
    $stmt->execute([$username, $email, $securityQuestion, $securityAnswer]);
    $user = $stmt->fetch();
}
?>

<?php if ($user) : ?>
    <section class="section">
        <h1 class="title">Set New Password</h1>
        <form action="reset-pwd.php" method="post">
            <div class="field">
                <label class="label">New Password</label>
                <div class="control">
                    <input class="input" type="password" name="new_password" required>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button type="submit" name="reset_password" class="button is-primary">Reset Password</button>
                </div>
            </div>
            <input type="hidden" name="user_id" value="$user['id']">
        </form>
    </section>
<?php else : ?>
    <p>Validation failed. Please ensure your details are correct.</p>
<?php endif; ?>

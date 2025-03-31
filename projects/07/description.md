# 📬 Project 07: Contact Message Management System

In this project, you will expand your existing contact system to include **message management tools** for site administrators. You’ll create a simple backend interface that allows admins to:
- View submitted contact messages
- Delete messages with confirmation
- Reply to messages via email using a form
- Track when and how messages were replied to

---

## 📁 Copy Project 06 to the Project 07 folder

- Recursively copy your entire `project-06` folder to a new `project-07` folder.
- Stage, commit, and push your new project to GitHub.

---

## 🧾 Database Update

This project uses the existing `contact_us` table from Project 0. You will now **extend** that table to track reply information.

Run the following SQL statement to update the table:

```sql
ALTER TABLE contact_us
ADD COLUMN reply TEXT NULL,
ADD COLUMN replied_at DATETIME NULL;
```

### 📌 Purpose:
- `reply` stores the actual admin response.
- `replied_at` stores the timestamp of when the message was replied to.

---

## 💻 Create PHP files from the terminal

From inside your `project-07` directory, run the following:

```bash
touch messages_manage.php message_delete.php message_reply.php
```

---

## 🗃️ Create `messages_manage.php`

This page will:
- Display all contact messages in a table
- Include a **Reply** and **Delete** button in the "Action" column

### ✅ PHP logic (top of `messages_manage.php`)

```php
<?php
include 'config.php';

// Only allow access for admin users

$stmt = $pdo->query("SELECT * FROM contact_us ORDER BY submitted_at DESC");
$messages = $stmt->fetchAll();
?>
```

### 🖼️ HTML structure

```html
<section class="section">
  <h1 class="title">Manage Messages</h1>
  <table class="table is-fullwidth is-striped">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Message</th>
        <th>Submitted</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($messages as $msg) : ?>
        <tr>
          <td><?= htmlspecialchars($msg['name']) ?></td>
          <td><?= htmlspecialchars($msg['email']) ?></td>
          <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
          <td><?= $msg['submitted_at'] ?></td>
          <td>
            <a class="button is-small is-link" href="message_reply.php?id=<?= $msg['id'] ?>">Reply</a>
            <a class="button is-small is-danger" href="message_delete.php?id=<?= $msg['id'] ?>">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
```

---

## 🗑️ Create `message_delete.php`

This file handles deletion confirmation and the actual delete operation.

### ✅ PHP logic (top of `message_delete.php`)

```php
<?php
include 'config.php';

// Only allow access for admin users

if (!isset($_GET['id'])) {
  header('Location: messages_manage.php');
  exit;
}

$id = $_GET['id'];

// Confirm deletion
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
  $stmt = $pdo->prepare("DELETE FROM contact_us WHERE id = ?");
  $stmt->execute([$id]);
  $_SESSION['messages'][] = "The message was deleted.";
  header('Location: messages_manage.php');
  exit;
}
```

### 🖼️ HTML structure

```html
<section class="section">
  <h1 class="title">Delete Message</h1>
  <p>Are you sure you want to delete this message?</p>
  <div class="buttons">
    <a class="button is-success" href="message_delete.php?id=<?= $id ?>&confirm=yes">Yes</a>
    <a class="button is-danger" href="messages_manage.php">No</a>
  </div>
</section>
```

---

## ✉️ Create `message_reply.php`

This file will:
- Load the email address and original message
- Show a form to enter the reply
- Send the reply using `mail()`
- Save the reply content and `replied_at` timestamp into the database

### ✅ PHP logic (top of `message_reply.php`)

```php
<?php
// Include config.php file

// Secure and only allow 'admin' users to access this page

if (!isset($_GET['id'])) {
  header('Location: messages_manage.php');
  exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM contact_us WHERE id = ?");
$stmt->execute([$id]);
$message = $stmt->fetch();

if (!$message) {
  $_SESSION['messages'][] = "Message not found.";
  exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $to = $message['email'];
  $subject = "Reply to your message";
  $body = $_POST['reply'];
  $headers = "From: admin@example.com"; // Replace with your own email address

  // Send the email...not rerally our server can't send emails...
  //mail($to, $subject, $body, $headers);

  // Save reply to database
  $stmt = $pdo->prepare("UPDATE contact_us SET reply = ?, replied_at = NOW() WHERE id = ?");
  $stmt->execute([$body, $id]);

  // You can also `echo` the result for testing instead of sending
  $_SESSION['messages'][] = 'Reply sent successfully!';
  header('Location: messages_manage.php');
  exit;
}
?>
```

### 🖼️ HTML structure

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
  <h1 class="title">Reply to Message</h1>
  <p><strong>To:</strong> <?= htmlspecialchars($message['email']) ?></p>
  <p><strong>Original Message:</strong><br><?= nl2br(htmlspecialchars($message['message'])) ?></p>
  <form method="post">
    <div class="field">
      <label class="label">Your Reply</label>
      <div class="control">
        <textarea class="textarea" name="reply" required></textarea>
      </div>
    </div>
    <div class="field">
      <div class="control">
        <button class="button is-link">Send Reply</button>
        <a class="button is-light" href="messages_manage.php">Cancel</a>
      </div>
    </div>
  </form>
</section>
<!-- END YOUR CONTENT -->
```

---

## 🧭 Update the Navigation Menu

Update your `nav.php`:

```html
<!-- BEGIN ADMIN MENU -->
<a href="messages_manage.php" class="navbar-item">Manage Messages</a>
<!-- END ADMIN MENU -->
```

---

## ✅ Final Steps

- Test all functionality: message listing, deletion, and replying.
- Verify that replies are stored in the database and reflected in the UI.
- Stage, commit, and push your changes to GitHub.
- Submit your GitHub URL as directed.

---

## 🎯 Summary

This project upgrades your contact form system with full message management features. By adding reply tracking, you’ll practice real-world patterns used in customer support systems and admin dashboards.

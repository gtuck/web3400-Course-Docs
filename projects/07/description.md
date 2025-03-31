Here is the updated **Project 07 description**, now including the database modification to track message replies:

---

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
- Optionally highlight whether a message has been replied to

(See original description above for full PHP logic and HTML structure.)

---

## 🗑️ Create `message_delete.php`

This file handles deletion confirmation and the actual delete operation.

(See original description above for logic and layout.)

---

## ✉️ Create `message_reply.php`

This file will:
- Load the email address and original message
- Show a form to enter the reply
- Send the reply using `mail()`
- Save the reply content and `replied_at` timestamp into the database

### ✅ Updated PHP logic for reply tracking:

```php
// Save reply to database
$stmt = $pdo->prepare("UPDATE contact_us SET reply = ?, replied_at = NOW() WHERE id = ?");
$stmt->execute([$body, $id]);
```

(Full logic and form layout are already provided above.)

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

Let me know if you’d like to extend this project further with filters, search, or pagination!

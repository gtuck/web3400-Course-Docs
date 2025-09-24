# Project 01 — Mini CMS (CRUD)

## Instructor Overview
Extend Project 00 into a working mini-CMS. Students will implement **CRUD** for a `posts` feature using **PHP + PDO**, the **PRG** pattern (Post-Redirect-Get), **prepared statements**, and **Bulma** styling—while reusing the shared templates and navigation.

**Outcome**
- `index.php` lists posts (titles link to single view).
- `blog_post.php` shows a single post by `post_id`.
- Admin pages: `admin_blog.php` (list + Create), `blog_create.php`, `blog_edit.php`, `blog_delete.php`.

---

## Learning Goals
- Implement **Create, Read, Update, Delete** with PDO and prepared statements.
- Build and validate **HTML forms**, sanitize and escape output, and **redirect** after POST.
- Reuse **head/nav/flash/footer** for design consistency.

---

## File Structure

```text
/ (Project 01 Root)
├─ config.php
├─ index.php              # List of posts
├─ blog_post.php          # Single post view by post_id
├─ admin_blog.php         # Admin list + Create button
├─ blog_create.php        # Create form + handler
├─ blog_edit.php          # Edit form + handler
├─ blog_delete.php        # Confirm + handler
├─ /templates
│   ├─ head.php
│   ├─ nav.php            # add Admin → Blog Admin
│   ├─ flash.php
│   └─ footer.php
└─ /sql
    ├─ schema.sql
    └─ seed.sql
```
---

## Database

**Table: `posts`**
```sql
CREATE TABLE IF NOT EXISTS posts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  body MEDIUMTEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);

CREATE INDEX idx_posts_created_at ON posts (created_at DESC);
CREATE INDEX idx_posts_updated_at ON posts (updated_at DESC);
```
---

## Page Requirements

### `index.php` (Read)
* Query the latest posts (e.g., 10 newest).
* Show the **title**, **created date**, and a **short excerpt**.
* The title links to `blog_post.php?post_id=ID`.

---

### `blog_post.php` (Single Post, required)
* Accept `post_id` (integer) in the query string.
* If invalid or missing, redirect to `index.php` with an error flash message.
* Show the **title**, **created/updated timestamps**, and **full body**.

---

### `admin_blog.php` (Admin List)
* Heading: `<h1>`"Blog Admin"`</h1>`.
* A "Create Post" button appears under the heading.
* Display a table with the following columns:
    * **ID**
    * **Title** (linked to the public view)
    * **Created**
    * **Updated**
    * **Actions** (Edit/Delete)
* Flash messages appear under the heading.

---

### `blog_create.php` (Create)
* **POST** form fields: `title` and `body`.
* **Validation**:
    * `title` must be at least 3 characters.
    * `body` must be at least 10 characters.
* Generate a unique **slug** from the title.
* **On success**:
    * `INSERT` the new post.
    * Display a flash message: "Post created."
    * Redirect to `admin_blog.php`.
* **On error**:
    * Re-render the form, showing errors and retaining previous user inputs.

---

### `blog_edit.php` (Update)
* Requires `post_id` in the query string.
* Loads the record based on `post_id`. If not found, show a `404`-style flash message.
* **On POST**:
    * Validate the form.
    * `UPDATE` the record.
    * If the title was changed, regenerate a unique **slug**.
* **On success**:
    * Display a flash message: "Post updated."
    * Redirect to `admin_blog.php`.

---

### `blog_delete.php` (Delete)
* Requires `post_id` in the query string.
* Shows a confirmation view that includes the **title** and **created date**.
* **On POST confirmation**:
    * `DELETE` the post from the database.
    * Display a flash message: "Post deleted."
    * Redirect to `admin_blog.php`.

---

## Notes for Students
- Apply /sql/schema.sql then /sql/seed.sql to your DB.
- After each successful POST (create/update/delete), you’ll be redirected to the Admin list and see a flash message.
- Keep using Bulma + shared templates for consistent UI.

---


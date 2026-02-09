# Project 01 — Mini CMS (CRUD)
Extend Project 00 into a working mini‑CMS. You will implement CRUD for a `posts` feature using PHP + PDO, the PRG pattern (Post‑Redirect‑Get), prepared statements, and Bulma styling — while reusing the shared templates and navigation from Project 00.

---

## Starting Point (Assumes Project 00)
- Project 00 is complete and working locally (config, templates, flash messages, contact form).
- Copy ALL files and functionality from Project 00 into Project 01.

```bash
cp -R projects/00/ projects/01/
```
---

## What You Will Build
- Public list page: `index.php` shows newest posts (title, date, excerpt).
- Public single page: `blog_post.php` shows one post by `post_id`.
- Admin pages: `admin_blog.php` (list + create button), `blog_create.php`, `blog_edit.php`, `blog_delete.php`.
- Shared templates used on every page: `templates/head.php`, `templates/nav.php`, `templates/flash.php`, `templates/footer.php`.

Quick scaffold (optional)
```bash
# From repo root
cd projects/01
touch admin_blog.php blog_create.php blog_delete.php blog_edit.php blog_post.php
mkdir sql
cd sql
touch posts.sql seed.sql
```

---

## Folder Structure
```text
/ (Project 01 Root)
├─ admin_blog.php         # Admin list + Create button
├─ blog_create.php        # Create form + handler (PRG)
├─ blog_delete.php        # Delete confirm + handler (PRG)
├─ blog_edit.php          # Edit form + handler (PRG)
├─ blog_post.php          # Single post view by post_id
├─ config.php             # Project config, PDO, flash, helpers
├─ index.php              # Homepage and list of posts
├─ contact.php            
├─ /sql
│   ├─ posts.sql          # Create `posts` table
│   └─ seed.sql           # Optional seed data
└─ /templates
    ├─ flash.php
    ├─ footer.php
    ├─ head.php
    └─ nav.php            # Add Admin → Blog Admin (relative link), keep Contact
```

---

## Files To Add/Update From Project 00
- Update: `index.php` — replace P00 landing content with latest posts list.
- New: `blog_post.php` — render single post by `post_id` with timestamps.
- New: `admin_blog.php` — admin table of posts with Create/Edit/Delete actions.
- New: `blog_create.php` — create form + handler; validates; PRG on success.
- New: `blog_edit.php` — edit form + handler; validates; updates slug if title changed.
- New: `blog_delete.php` — delete confirmation + handler; PRG on success.
- New: `sql/posts.sql` — creates `posts` table (see script below).
- New: `sql/seed.sql` — optional seed data for testing.
- New: `sql/contact_us.sql` — creates `contact_us` table for the Contact form.
- Update: `config.php` — keep PDO + flash; add `slugify()` helper.
- Update: `templates/nav.php` — add Admin → Blog Admin link; use `admin_blog.php"` (relative).

---

## Database Setup
Create the `posts` table and optional seed data.  

-See SQL code in [sql/posts.sql](sql/posts.sql) and [sql/seed.sql](sql/seed.sql) files


Steps
- Log in to phpMyAdmin and run the SQL from `/sql/posts.sql`.
- Optionally run `/sql/seed.sql` to add example posts.

---

## Navigation Updates
- In `templates/nav.php`, add an Admin menu with a link to `admin_blog.php`.
- Use relative links (e.g., `href="admin_blog.php"`).

```html
<div class="navbar-item has-dropdown is-hoverable">
    <a class="navbar-link">Admin</a>
        <div class="navbar-dropdown">
            <a class="navbar-item" href="admin_blog.php">Blog Admin</a>
        </div>
</div>
```

---

## Config & Helpers
- Keep your Project 00 `config.php` and extend it with a small `slugify()` helper for generating URL‑friendly slugs from titles.

```php
/**
 * EXAMPLE: SLUGIFY — Convert a string to a URL-friendly slug.
 * Example: "Hello World!" → "hello-world"
 */
function slugify(string $text): string {
  $text = trim($text);
  if (function_exists('iconv')) {
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
  }
  // Replace non letters/digits with hyphens
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
  $text = trim($text, '-');
  $text = strtolower($text);
  // Remove any remaining invalid chars
  $text = preg_replace('~[^-a-z0-9]+~', '', $text);
  return $text ?: 'post';
}
```
---

## Page Requirements

### `index.php` (Read)
- Query the latest posts (e.g., 10 newest).
- Show title (links to `blog_post.php?post_id=ID`), created date, and a short excerpt.
- [index.php](index.php)

### `blog_post.php` (Read single)
- Accept `post_id` (integer) in the query string.
- If invalid or missing, redirect to `index.php` with an error flash message.
- Show title, created/updated timestamps, and full body.

### `admin_blog.php` (Admin list)
- Heading: “Blog Admin” with a “Create Post” button.
- Table columns: ID, Title (link to public view), Created, Updated, Actions (Edit/Delete).
- Flash messages show under the heading.
- [admin_blog.php](admin_blog.php)

### `blog_create.php` (Create)
- POST fields: `title`, `body`.
- Validation: title ≥ 3 chars; body ≥ 10 chars.
- Generate a unique slug from the title.
- On success: INSERT, flash “Post created.” (use Bulma types like `is-success`), redirect to `admin_blog.php` (PRG).
- On error: re‑render form showing validation messages and previous values.

### `blog_edit.php` (Update)
- Requires `post_id`; load record or flash not found and redirect.
- On POST: validate; update title/body; if title changed, regenerate a unique slug.
- On success: flash “Post updated.” and redirect to `admin_blog.php`.

### `blog_delete.php` (Delete)
- Requires `post_id`; show confirmation including title and created date.
- On POST confirm: DELETE, flash “Post deleted.”, redirect to `admin_blog.php`.

---

## Implementation Notes
- Use prepared statements for all DB queries and mutations.
- Escape output with `htmlspecialchars` (use `ENT_QUOTES`).
- Follow PRG: after successful POST, set a flash and `header('Location: ...')`.
- Keep include order: `head.php` → `nav.php` → `flash.php` → page content → `footer.php`.
- Keep consistent Bulma styles and components; use BulmaJS to dismiss flash notifications.

---

## Key PHP Examples

Prepared SELECT (read)
```php
// Load one post safely by id
$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ? LIMIT 1');
$stmt->execute([(int)$post_id]);
$post = $stmt->fetch();
```

- Why: Prevents SQL injection and keeps SQL/data separate.
- Used in: [blog_post.php](blog_post.php), [blog_edit.php](blog_edit.php), [blog_delete.php](blog_delete.php)

Prepared INSERT (create)
```php
$stmt = $pdo->prepare('INSERT INTO posts (title, slug, body) VALUES (?, ?, ?)');
$stmt->execute([$title, $slug, $body]);
```

- Why: Safely writes user-submitted data using placeholders.
- Used in: [blog_create.php](blog_create.php), [contact.php](contact.php)

Prepared UPDATE (edit)
```php
$stmt = $pdo->prepare('UPDATE posts SET title = ?, slug = ?, body = ? WHERE id = ?');
$stmt->execute([$title, $slug, $body, (int)$post_id]);
```

- Why: Updates only intended columns; parameters are bound at execution.
- Used in: [blog_edit.php](blog_edit.php)

Prepared DELETE (remove)
```php
$stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
$stmt->execute([(int)$post_id]);
```

- Why: Removes a specific record by id with safe binding.
- Used in: [blog_delete.php](blog_delete.php)

PRG pattern (Post → Redirect → Get)
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // ... validate + perform DB work ...
  flash('Action completed.', 'is-success');
  header('Location: admin_blog.php');
  exit; // Always exit after header redirect
}
```

- Why: Prevents duplicate form resubmissions; results in shareable URLs.
- Used in: [blog_create.php](blog_create.php), [blog_edit.php](blog_edit.php), [blog_delete.php](blog_delete.php), [contact.php](contact.php)

Validation and sanitization
```php
$errors = [];
if ($title === '' || mb_strlen($title) < 3) {
  $errors['title'] = 'Title is required (min 3 chars).';
}
if ($body === '' || mb_strlen($body) < 10) {
  $errors['body'] = 'Body is required (min 10 chars).';
}
```

- Why: Enforces server-side rules for reliability and security.
- Used in: [blog_create.php](blog_create.php), [blog_edit.php](blog_edit.php), [contact.php](contact.php)

Unique slug generation
```php
$base = slugify($title);
$slug = $base;
$i = 2;
$check = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE slug = ?' . ($excludeId ? ' AND id <> ?' : ''));
while (true) {
  $params = $excludeId ? [$slug, (int)$excludeId] : [$slug];
  $check->execute($params);
  if ($check->fetchColumn() == 0) break;
  $slug = $base . '-' . $i++;
}
```

- Why: Ensures human-readable, unique slugs for each post.
- Used in: [blog_create.php](blog_create.php) (no exclude), [blog_edit.php](blog_edit.php) (exclude current id)

Reading and validating query params
```php
$post_id = (int)($_GET['post_id'] ?? 0);
if ($post_id <= 0) {
  flash('Invalid post id.', 'is-danger');
  header('Location: index.php');
  exit;
}
```

- Why: Query strings are strings; cast and validate before DB usage.
- Used in: [blog_post.php](blog_post.php), [blog_edit.php](blog_edit.php), [blog_delete.php](blog_delete.php)

Escaping output safely
```php
// Title in an attribute or text node
<?= htmlspecialchars($post['title'], ENT_QUOTES) ?>

// Preserve newlines for plain text bodies
<?= nl2br(htmlspecialchars($post['body'], ENT_QUOTES)) ?>
```

- Why: Prevents XSS by escaping untrusted content in HTML output.
- Used in: All pages and templates that output user content

## Test Locally
From repository root:
```bash
php -S 0.0.0.0:8080
```
Visit (adjust port as needed):
- Create, edit, and delete a post; verify flashes and redirects.
- Click a post title on the home page; verify `blog_post.php` renders.

---

## Grading Checklist
- [ ] Project 01 exists at `projects/01/` and runs.
- [ ] `config.php` loads; session + PDO configured; `.htaccess` denies access to `config.php`.
- [ ] `/sql/posts.sql` applied (and optional `/sql/seed.sql`).
- [ ] `templates/` reused; include order correct; Bulma + BulmaJS present.
- [ ] `nav.php` has an Admin → Blog Admin link (relative path), and a Contact link.
- [ ] `index.php` lists newest posts with excerpts; titles link to single view.
- [ ] `blog_post.php` validates `post_id` and renders title/body with timestamps.
- [ ] `admin_blog.php` lists posts with Edit/Delete links and Create button.
- [ ] `blog_create.php` validates input, creates post with unique slug, PRG with success flash.
- [ ] `blog_edit.php` validates and updates; slug regenerates if title changes.
- [ ] `blog_delete.php` confirms then deletes; PRG with success flash.
- [ ] All DB access uses prepared statements; all output is escaped.
- [ ] `contact.php` is present and works (form validates, flashes, and persists to DB as in Project 00).
- [ ] `contact_us` table exists (created via `/sql/contact_us.sql` if needed).

---

## Submit
Submit the direct URL to your Project 01 folder (replace YOUR‑USER and repo name):
```
https://github.com/YOUR-USER/YOUR-REPO/blob/main/projects/01/
```

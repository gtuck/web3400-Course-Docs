# Project 01 — Mini CMS (CRUD)
Extend Project 00 into a working mini‑CMS. You will implement CRUD for a `posts` feature using PHP + PDO, the PRG pattern (Post‑Redirect‑Get), prepared statements, and Bulma styling — while reusing the shared templates and navigation from Project 00.

---

## Starting Point (Assumes Project 00)
- Project 00 is complete and working locally (config, templates, flash messages, contact form).
- `config.php` initializes session/flash and connects to MySQL via PDO.
- `.htaccess` exists and denies direct access to `config.php` (keep this in Project 01).

If you do not have these in Project 01 yet, copy them forward from Project 00.

```bash
cp -R projects/00 projects/01
```
Then remove anything not needed and proceed with the tasks below.

---

## What You Will Build
- Public list page: `index.php` shows newest posts (title, date, excerpt).
- Public single page: `blog_post.php` shows one post by `post_id`.
- Admin pages: `admin_blog.php` (list + create button), `blog_create.php`, `blog_edit.php`, `blog_delete.php`.
- Shared templates used on every page: `templates/head.php`, `templates/nav.php`, `templates/flash.php`, `templates/footer.php`.

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
├─ /sql
│   ├─ schema.sql         # Create `posts` table
│   └─ seed.sql           # Optional seed data
└─ /templates
    ├─ flash.php
    ├─ footer.php
    ├─ head.php
    └─ nav.php            # Add Admin → Blog Admin (relative link)
```

---

## Database Setup
Create the `posts` table and optional seed data.

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

Steps
- Log in to phpMyAdmin and run the SQL from `/sql/schema.sql`.
- Optionally run `/sql/seed.sql` to add example posts.

---

## Navigation Updates
- In `templates/nav.php`, add an Admin menu with a link to `admin_blog.php`.
- Use relative links (e.g., `href="admin_blog.php"`), not root `/admin_blog.php`.
- If you kept your Project 00 `contact.php`, keep the Contact link; otherwise remove or update it.

---

## Config & Helpers
- Keep your Project 00 `config.php` and extend it with a small `slugify()` helper for generating URL‑friendly slugs from titles.
- Ensure sessions and flash messages are initialized.
- Keep `.htaccess` in this folder to deny direct access to `config.php`.

Example `.htaccess` (Apache 2.4+):
```apache
php_flag display_errors on
<Files "config.php">
  Require all denied
</Files>
```

---

## Page Requirements

### `index.php` (Read)
- Query the latest posts (e.g., 10 newest).
- Show title (links to `blog_post.php?post_id=ID`), created date, and a short excerpt.

### `blog_post.php` (Read single)
- Accept `post_id` (integer) in the query string.
- If invalid or missing, redirect to `index.php` with an error flash message.
- Show title, created/updated timestamps, and full body.

### `admin_blog.php` (Admin list)
- Heading: “Blog Admin” with a “Create Post” button.
- Table columns: ID, Title (link to public view), Created, Updated, Actions (Edit/Delete).
- Flash messages show under the heading.

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

## Test Locally
From repository root:
```bash
php -S 0.0.0.0:8080
```
Visit (adjust port as needed):
- http://localhost:8080/projects/01/index.php
- http://localhost:8080/projects/01/admin_blog.php
- Create, edit, and delete a post; verify flashes and redirects.
- Click a post title on the home page; verify `blog_post.php` renders.

---

## Grading Checklist
- [ ] Project 01 exists at `projects/01/` and runs.
- [ ] `config.php` loads; session + PDO configured; `.htaccess` denies access to `config.php`.
- [ ] `/sql/schema.sql` applied (and optional `/sql/seed.sql`).
- [ ] `templates/` reused; include order correct; Bulma + BulmaJS present.
- [ ] `nav.php` has an Admin → Blog Admin link (relative path), Contact link handled appropriately.
- [ ] `index.php` lists newest posts with excerpts; titles link to single view.
- [ ] `blog_post.php` validates `post_id` and renders title/body with timestamps.
- [ ] `admin_blog.php` lists posts with Edit/Delete links and Create button.
- [ ] `blog_create.php` validates input, creates post with unique slug, PRG with success flash.
- [ ] `blog_edit.php` validates and updates; slug regenerates if title changes.
- [ ] `blog_delete.php` confirms then deletes; PRG with success flash.
- [ ] All DB access uses prepared statements; all output is escaped.

---

## Submit
Submit the direct URL to your Project 01 folder (replace YOUR‑USER and repo name):
```
https://github.com/YOUR-USER/YOUR-REPO/blob/main/projects/01/
```
Open in a private/incognito window to confirm accessibility.

# Project 01 — Mini CMS (CRUD)
This repo contains a starting point with full PHP files that implement the CRUD mini‑CMS described in class.
- Public pages: `index.php`, `blog_post.php` (uses `post_id`).
- Admin pages: `admin_blog.php`, `blog_create.php`, `blog_edit.php`, `blog_delete.php`.
- Templates: `templates/head.php`, `templates/nav.php`, `templates/flash.php`, `templates/footer.php`.
- SQL: `sql/schema.sql`, `sql/seed.sql`.

**Setup**
1) Create a MySQL/MariaDB database and update creds in `config.php` (or set `DB_DSN`, `DB_USER`, `DB_PASS` env vars).
2) Run `sql/schema.sql` then `sql/seed.sql`.
3) Serve this folder via PHP built‑in server: `php -S localhost:8000` and visit http://localhost:8000.

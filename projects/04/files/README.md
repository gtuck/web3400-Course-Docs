# php-mvc (Project 04 Reference)

This folder contains a reference implementation for Project 04:
- Dotenv configuration via `vlucas/phpdotenv`
- Centralized PDO helper (`App\\Support\\Database`)
- Lightweight BaseModel (`App\\Models\\BaseModel`) + generator (`scripts/generate-model.php`) [REQUIRED]
- Contact page with GET/POST routes and model-based INSERT into `contact_us`

## Quick Start

1. From this reference folder, install dependencies:
   ```bash
   cd projects/04/files
   composer install || true
   composer require vlucas/phpdotenv
   composer dump-autoload
   ```

2. Copy `.env.example` to `.env` and fill in values:
   ```bash
   cp .env.example .env
   # edit .env
   ```

3. Run the built-in PHP server from `public/`:
   ```bash
   php -S 0.0.0.0:8000 -t public
   ```

4. Open:
   - Home: http://localhost:8000/
   - Contact: http://localhost:8000/contact

## Notes
- `public/index.php` loads Dotenv and requires DB_* env vars.
- `App\\Support\\Database::pdo()` reads env vars only (no hardcoded defaults).
- `App\\Models\\BaseModel` implements reusable CRUD with `$fillable` whitelisting.
- Use `php scripts/generate-model.php contact_us` to scaffold `App\\Models\\Contact` and call `Contact::create([...])` in your controller.
- The Contact view is plain HTML (no CSS) by design.
- Ensure your database has the `contact_us` table (see `projects/01/sql/contact_us.sql`).

# Project 07 – Content Management System (Posts CMS)
Build a simple CMS that lets Editors and Administrators create, edit, publish, and delete Posts. Start from your completed Project 06 and extend your MVC app with a Posts model, admin management screens, a post detail page, and an updated home page that showcases featured content.

—

## Overview
Starting from a completed Project 06, you will:

1. Create a `posts` table and seed sample data
2. Extend the `Post` model and add helper queries
3. Add a “time ago” helper for published dates
4. Update the home page to show featured, recent posts (limit 10)
5. Add a public post detail route/view
6. Build an admin posts area (list/create/edit/publish/unpublish/delete)
7. Update navigation with an Admin dropdown (Manage Users, Manage Posts)

Constraints:
- Use your existing MVC structure and patterns from Projects 05–06.
- Keep controllers thin and put DB logic in Models.
- Protect all state-changing actions with CSRF and role checks.
- Roles: `admin`, `editor`, `user`. Editors and Admins can manage posts; only Admins manage users.

—

## Learning Objectives
- Design a small CMS on top of your MVC foundation
- Model content states and publication workflow
- Implement role-gated admin features with clean controllers
- Render lists and detail pages with safe output and small helpers

—

## Prerequisites
- Completed Project 06 with:
  - Users + Auth + Roles (`admin`, `editor`, `user`)
  - CSRF, Validator, View engine, Router
  - Working DB connection via `Support/Database.php`

—

## Target Structure

```
projects/07/
  public/
    index.php                # unchanged
  src/
    Controller.php           # already has auth helpers
    Models/
      BaseModel.php
      Post.php               # updated with CMS fields + helpers
    Controllers/
      HomeController.php     # updated: pulls featured posts
      PostsController.php    # new: public post detail
      Admin/
        PostsController.php  # new: admin CRUD + publish/unpublish
        UsersController.php  # from P06 (unchanged)
    Routes/
      index.php              # routes for public + admin posts
    Support/
      View.php
      Time.php               # new helper: time-ago
    Views/
      index.php              # updated featured posts layout
      posts/show.php         # new post detail view
      admin/posts/
        index.php            # list
        create.php           # create
        edit.php             # edit
        _form.php            # shared form
      partials/nav.php       # updated: Admin dropdown
```

—

## Step 1) Scaffold from Project 06 + create new files

From the repository root, copy P06 to P07 and scaffold the new files/directories you’ll fill in. One copy/paste command:

```bash
cp -r projects/06 projects/07 && \
mkdir -p projects/07/src/Controllers/Admin \
         projects/07/src/Views/admin/posts \
         projects/07/src/Views/posts \
         projects/07/src/Support \
  && \
touch projects/07/src/Controllers/PostsController.php \
      projects/07/src/Controllers/Admin/PostsController.php \
      projects/07/src/Support/Time.php \
      projects/07/src/Views/admin/posts/{index.php,create.php,edit.php,_form.php} \
      projects/07/src/Views/posts/show.php
```

—

## Step 2) Create the `posts` table

Run the following SQL in your database. This schema includes fields referenced by the seed data (excerpt + simple engagement metrics) and indexes needed for querying published/featured posts.

```sql
DROP TABLE IF EXISTS posts;

CREATE TABLE IF NOT EXISTS posts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  author_id INT UNSIGNED NOT NULL,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  excerpt VARCHAR(255) NULL,
  body MEDIUMTEXT NOT NULL,
  featured_image VARCHAR(255) NOT NULL DEFAULT 'https://picsum.photos/200',
  status ENUM('draft','published','archived','deleted') NOT NULL DEFAULT 'draft',
  published_at DATETIME NULL,
  is_featured BOOLEAN NOT NULL DEFAULT 0,
  favs INT UNSIGNED NOT NULL DEFAULT 0,
  likes INT UNSIGNED NOT NULL DEFAULT 0,
  comments_count INT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id),
  INDEX idx_status (status),
  INDEX idx_published_at (published_at)
);
```

—

## Step 3) Seed the `posts` table

Insert a mix of published, draft, and archived posts so your lists and filters have real data:

```sql
INSERT INTO posts (
  author_id,
  title,
  slug,
  excerpt,
  body,
  featured_image,
  status,
  published_at,
  favs,
  likes,
  comments_count,
  is_featured
) VALUES
  (
    1,
    'Getting Started with PHP',
    'getting-started-with-php',
    'A beginner’s guide to understanding PHP fundamentals and syntax.',
    'PHP is a popular scripting language used primarily for web development. In this post, we’ll explore how to set up a local environment, write your first PHP script, and understand variables and functions.',
    'https://picsum.photos/seed/php/200',
    'published',
    NOW(),
    5,
    10,
    2,
    1
  ),
  (
    1,
    'Mastering MySQL Joins',
    'mastering-mysql-joins',
    'Learn how to use INNER, LEFT, and RIGHT joins effectively in MySQL.',
    'In relational databases, joins allow you to combine data from multiple tables. This tutorial covers common join types and best practices for optimizing queries.',
    'https://picsum.photos/seed/mysql/200',
    'published',
    NOW(),
    3,
    8,
    1,
    0
  ),
  (
    1,
    'Building a Blog with PHP and MySQL',
    'building-a-blog-with-php-and-mysql',
    'Step-by-step guide to building your own dynamic blog system using PHP and MySQL.',
    'This post walks through database setup, CRUD operations, and routing for a simple yet functional blog system using PHP and MySQL.',
    'https://picsum.photos/seed/blog/200',
    'published',
    NOW(),
    7,
    15,
    4,
    1
  ),
  (
    1,
    'Understanding RESTful APIs',
    'understanding-restful-apis',
    'A clear introduction to REST architecture and API best practices.',
    'APIs are at the heart of modern web applications. In this post, we discuss the principles of REST, HTTP methods, and how to build and consume APIs effectively.',
    'https://picsum.photos/seed/api/200',
    'draft',
    NULL,
    0,
    0,
    0,
    0
  ),
  (
    1,
    'Debugging PHP Applications',
    'debugging-php-applications',
    'Tips and tools for debugging PHP code efficiently.',
    'Debugging is an essential skill for every developer. Learn how to use built-in PHP error handling, Xdebug, and logging to identify and resolve common issues.',
    'https://picsum.photos/seed/debug/200',
    'archived',
    '2024-12-15 10:00:00',
    2,
    5,
    0,
    0
  );
```

—

## Step 4) Update the `Post` model (fields + helpers)

Add CMS fields and a couple of helper queries for the home page and post detail pages.

`src/Models/Post.php`
```php
<?php
namespace App\Models;

use PDO;

final class Post extends BaseModel
{
    protected static string $table = 'posts';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'author_id','title','slug','excerpt','body','featured_image',
        'status','published_at','is_featured','favs','likes','comments_count',
    ];

    public static function findBySlug(string $slug): ?array
    {
        return static::firstBy('slug', $slug);
    }

    public static function recentFeaturedWithAuthors(int $limit = 10): array
    {
        $sql = "SELECT p.*, u.name AS author_name
                FROM posts p JOIN users u ON u.id = p.author_id
                WHERE p.status='published' AND p.is_featured=1 AND p.published_at IS NOT NULL
                ORDER BY p.published_at DESC
                LIMIT :limit";
        $stmt = static::pdo()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findBySlugWithAuthor(string $slug): ?array
    {
        $sql = "SELECT p.*, u.name AS author_name
                FROM posts p JOIN users u ON u.id = p.author_id
                WHERE p.slug = :slug LIMIT 1";
        $st = static::pdo()->prepare($sql);
        $st->bindValue(':slug', $slug);
        $st->execute();
        $row = $st->fetch();
        return $row ?: null;
    }
}
```

—

## Step 5) Add a tiny “time ago” helper

Human-readable published dates: “Just now”, “10 minutes ago”, “3 hours ago”, or the date when older than 24 hours.

`src/Support/Time.php`
```php
<?php
namespace App\Support;

final class Time
{
    public static function ago(?string $datetime): string
    {
        if (!$datetime) return '';
        $ts = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
        if ($ts === false) return '';
        $diff = time() - $ts;
        if ($diff < 60) return 'Just now';
        $mins = (int) floor($diff / 60);
        if ($mins < 60) return $mins . ' minute' . ($mins === 1 ? '' : 's') . ' ago';
        $hours = (int) floor($mins / 60);
        if ($hours < 24) return $hours . ' hour' . ($hours === 1 ? '' : 's') . ' ago';
        return date('M j, Y', $ts);
    }
}
```

—

## Step 5a) Slugify

Add a reusable helper to your base controller, then call it from Admin/PostsController:

```php
// filepath: src/Controller.php
protected function slugify(string $value): string
{
    $v = strtolower(trim($value));
    $v = preg_replace('~[^a-z0-9]+~', '-', $v) ?? '';
    $v = trim($v, '-');
    return $v ?: uniqid('post-');
}
```

-

## Step 6) Update the home page (featured posts)

Controller: fetch the 10 most recent, featured, published posts, then precompute `published_human` for the view.

`src/Controllers/HomeController.php` (index action)
```php
use App\Models\Post;
use App\Support\Time;

$posts = Post::recentFeaturedWithAuthors(10);
foreach ($posts as &$p) {
    $p['published_human'] = Time::ago($p['published_at']);
}
$this->render('index', ['title' => 'Home', 'posts' => $posts]);
```

View: update the `<article class="box mb-4">` content.

`src/Views/index.php`
```php
<?php foreach ($posts as $post): ?>
  <article class="box mb-4">
    <div class="media">
      <figure class="media-left">
        <p class="image is-128x128">
          <img src="<?= $this->e($post['featured_image'] ?? 'https://picsum.photos/200') ?>" alt="Featured image">
        </p>
      </figure>
      <div class="media-content">
        <h3 class="title is-4"><?= $this->e($post['title']) ?></h3>
        <p class="is-size-7 has-text-grey">
          By <?= $this->e($post['author_name'] ?? 'Unknown') ?> •
          <?= $this->e($post['published_human'] ?? '') ?>
        </p>
        <p class="mt-2">
          <?php $body = strip_tags((string)($post['body'] ?? '')); ?>
          <?= $this->e(mb_substr($body, 0, 100)) ?><?= mb_strlen($body) > 100 ? '…' : '' ?>
          <a href="/posts/<?= $this->e($post['slug']) ?>">read more</a>
        </p>
      </div>
    </div>
  </article>
<?php endforeach; ?>
```

—

## Step 7) Public post detail

Route + controller action + view to render a single, published post by `slug`.

Routes (`src/Routes/index.php`):
```php
use App\Controllers\PostsController;
$router->get('/posts/{slug}', PostsController::class, 'show');
```

Controller (`src/Controllers/PostsController.php`):
```php
<?php
namespace App\Controllers;

use App\Controller;
use App\Models\Post;
use App\Support\Time;

class PostsController extends Controller
{
    public function show(string $slug): void
    {
        $post = Post::findBySlugWithAuthor($slug);
        if (!$post || $post['status'] !== 'published') {
            http_response_code(404);
            echo 'Post not found';
            return;
        }
        $this->render('posts/show', [
            'title' => $post['title'],
            'post' => $post,
            'published_human' => Time::ago($post['published_at']),
        ]);
    }
}
```

View (`src/Views/posts/show.php`):
```php
<?php $this->layout('layouts/main'); $this->start('content'); ?>
<section class="section">
  <div class="container content">
    <p class="is-size-7 has-text-grey"><a href="/">← Back</a></p>
    <h1 class="title"><?= $this->e($post['title']) ?></h1>
    <p class="is-size-7 has-text-grey">
      By <?= $this->e($post['author_name'] ?? 'Unknown') ?> • <?= $this->e($published_human ?? '') ?>
    </p>
    <figure class="image is-3by1 mb-4">
      <img src="<?= $this->e($post['featured_image'] ?? 'https://picsum.photos/1200/400') ?>" alt="Featured image">
    </figure>
    <div>
      <?= nl2br($this->e($post['body'])) ?>
    </div>
  </div>
  </section>
<?php $this->end(); ?>
```

—

## Step 8) Admin Posts routes

Add role-gated routes (require `editor` or `admin`) to list, create, edit, publish/unpublish, and delete posts.

`src/Routes/index.php`
```php
use App\Controllers\Admin\PostsController as AdminPostsController;

$router->get('/admin/posts', AdminPostsController::class, 'index');
$router->get('/admin/posts/create', AdminPostsController::class, 'create');
$router->post('/admin/posts', AdminPostsController::class, 'store');
$router->get('/admin/posts/{id}/edit', AdminPostsController::class, 'edit');
$router->post('/admin/posts/{id}', AdminPostsController::class, 'update');
$router->post('/admin/posts/{id}/publish', AdminPostsController::class, 'publish');
$router->post('/admin/posts/{id}/unpublish', AdminPostsController::class, 'unpublish');
$router->post('/admin/posts/{id}/delete', AdminPostsController::class, 'destroy');
```

—

## Step 9) Admin Posts controller

Follow your established controller pattern (thin controller, validation, CSRF checks, role guard in `__construct`).

`src/Controllers/Admin/PostsController.php` (key parts)
```php
class PostsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireRole('admin','editor');
    }

    public function index(): void
    {
        $posts = \App\Models\Post::all(limit: 200, orderBy: '`published_at` DESC, `id` DESC');
        $this->render('admin/posts/index', ['title' => 'Manage Posts', 'posts' => $posts]);
    }

    public function create(): void
    {
        $this->render('admin/posts/create', ['title' => 'Create Post']);
    }

    public function store(): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) { /* flash + redirect */ }
        $user = $this->user();
        $data = [
            'author_id' => (int)($user['id'] ?? 0),
            'title' => trim($_POST['title'] ?? ''),
            'slug' => $this->slugify($_POST['slug'] ?? ($_POST['title'] ?? '')),
            'excerpt' => trim($_POST['excerpt'] ?? ''),
            'body' => trim($_POST['body'] ?? ''),
            'featured_image' => trim($_POST['featured_image'] ?? ''),
            'status' => $_POST['status'] ?? 'draft',
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        ];
        // validate + unique slug; if status=published set published_at = now
        if ($data['status'] === 'published') { $data['published_at'] = date('Y-m-d H:i:s'); }
        \App\Models\Post::create($data);
        $this->flash('Post created.', 'is-success');
        $this->redirect('/admin/posts');
    }

    public function edit(int $id): void { /* load + render */ }
    public function update(int $id): void { /* validate + update + published_at */ }
    public function publish(int $id): void { /* set status=published, published_at=now */ }
    public function unpublish(int $id): void { /* set status=draft, published_at=NULL */ }
    public function destroy(int $id): void { /* soft delete: status=deleted */ }
}
```

—

## Step 10) Admin Posts views

Create a shared form partial and simple screens for list/create/edit.

`src/Views/admin/posts/_form.php`
```php
<?php $isEdit = isset($post); $action = $isEdit ? "/admin/posts/{$this->e($post['id'])}" : '/admin/posts'; ?>
<form method="post" action="<?= $action ?>">
  <?php $this->csrfField(); ?>
  <!-- Title, Slug, Excerpt, Image, Body, Featured checkbox, Status select -->
  <!-- Submit + Cancel buttons -->
</form>
```

`src/Views/admin/posts/index.php`
```php
<a class="button is-primary" href="/admin/posts/create">New Post</a>
<table class="table is-fullwidth is-striped is-hoverable">
  <thead><tr><th>Title</th><th>Status</th><th>Featured</th><th>Published</th><th class="has-text-right">Actions</th></tr></thead>
  <tbody>
    <?php foreach ($posts as $p): ?>
      <tr>
        <td><a href="/posts/<?= $this->e($p['slug']) ?>" target="_blank"><?= $this->e($p['title']) ?></a></td>
        <td><?= $this->e(ucfirst($p['status'])) ?></td>
        <td><?= ((int)($p['is_featured'] ?? 0) === 1) ? 'Yes' : 'No' ?></td>
        <td><?= $this->e($p['published_at'] ?: '—') ?></td>
        <td class="has-text-right">
          <!-- Edit / Publish-Unpublish / Delete (with CSRF) -->
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
```

—

## Step 11) Update the navigation (Admin dropdown)

Replace the single Admin/Users link with a dropdown. Show “Manage Users” only to Admins; show “Manage Posts” to Admins and Editors.

`src/Views/partials/nav.php` (snippet)
```php
<?php if (in_array($role, ['admin','editor'], true)): ?>
  <div class="navbar-item has-dropdown is-hoverable">
    <a class="navbar-link">Admin</a>
    <div class="navbar-dropdown">
      <?php if ($role === 'admin'): ?>
        <a class="navbar-item" href="/admin/users">Manage Users</a>
      <?php endif; ?>
      <a class="navbar-item" href="/admin/posts">Manage Posts</a>
    </div>
  </div>
<?php endif; ?>
```

—

## Step 12) Rubric (100 points)

- Database and Model (20)
  - `posts` table created with schema above (10)
  - `Post` model updated with fields + helper queries (10)
- Home Page (20)
  - Shows 10 most recent featured, published posts (10)
  - Card includes featured image, title, author, time-ago, body preview, “read more” (10)
- Public Post (10)
  - Route + controller + view by `slug` (10)
- Admin Posts (35)
  - Index list with actions (10)
  - Create + Edit with validation + CSRF (10)
  - Publish/Unpublish + Delete (soft delete via status) (15)
- Navigation (5)
  - Admin dropdown with Manage Users/Posts; visibility by role (5)
- Code Quality (10)
  - Follows MVC patterns, safe output, thin controllers (10)

—

Note: Ensure you’ve run the SQL in Steps 2–3 and have at least one `editor` or `admin` user to access the admin posts area.

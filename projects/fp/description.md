# Final Project – Administrator Dashboard (MVC CMS)
Build a production‑ready **Administrator Dashboard** on top of your existing MVC CMS. This final project is a comprehensive integration of everything you built in Projects 03–08: routing, controllers, models, authentication, authorization, validation, CSRF protection, content management, and engagement features.

The admin dashboard must expose meaningful **Key Performance Indicators (KPIs)** and recent activity so an administrator can monitor and manage the site efficiently.

---

## Overview
Starting from your completed **Project 08** (or best version of your CMS from Projects 06–08), you will:

1. **Add an authenticated Admin Dashboard route + controller action** in your MVC app
2. **Secure the dashboard** so only users with an `admin` role can access it
3. **Implement KPI cards** for posts (articles), contact messages, and users
4. **Display recent contact messages** and relevant recent activity in the dashboard
5. **Make the dashboard the default landing page after admin login** (instead of the profile page)
6. **Polish UI/UX** to feel like a cohesive, professional admin experience

Instead of a standalone `admin_dashboard.php` file, your final project should treat the dashboard as a **first‑class MVC feature**: routed through `public/index.php`, implemented in a controller, backed by models, and rendered through your view engine.

---

## Learning Objectives
- Integrate all previous project features into a cohesive MVC application
- Design and secure an **admin‑only** workflow and navigation
- Implement **read‑heavy** dashboard views using aggregate queries (KPIs)
- Use models and prepared statements for **analytics‑style queries**
- Present data using a clean, responsive dashboard layout
- Practice **quality assurance**: testing, debugging, and polishing for production‑like readiness

---

## Prerequisites
- A working CMS codebase from **Project 08** (or equivalent) with:
  - Front controller (`public/index.php`) and `Router`
  - Authenticated users with roles (e.g., `admin`, `user`)
  - Content models (e.g., `Post`/`Article`, `User`, `ContactMessage`)
  - Enhanced user profile page with activity tabs (Likes, Favs, Comments) from Project 08
  - Active Record‑style `BaseModel` and database helper
  - CSRF protection, validation, and view templating from Projects 05–08
- Database tables for:
  - Content (articles/posts)
  - Users and roles
  - Contact messages (e.g., `contact_us`)
  - Engagement tables (`post_likes`, `post_favorites`, `comments`, `user_interactions`)

If you are missing any of these, backfill them from your earlier projects before starting.

---

## Target Structure

Your final project should live in `projects/fp/` and mirror the structure of Projects 05–08:

```
projects/fp/
  composer.json
  public/
    index.php              # Front controller, bootstraps app
  src/
    Router.php             # Routes including admin dashboard
    Controller.php         # Base controller (view, flash, redirect, CSRF)
    Support/
      Database.php
      View.php
      Validator.php
    Models/
      BaseModel.php
      User.php
      Article.php          # or Post.php
      ContactMessage.php   # or Contact.php / ContactUs.php
      PostLike.php         # optional – from P08
      PostFavorite.php     # optional – from P08
      Comment.php          # optional – from P08
    Controllers/
      AuthController.php
      ArticleController.php
      ContactController.php
      Admin/
        DashboardController.php   # NEW – Admin dashboard controller
    Routes/
      index.php            # + admin route(s)
    Views/
      layouts/
        main.php
      partials/
        nav.php
        flash.php
      admin/
        dashboard.php      # NEW – Admin dashboard view
        _kpi_cards.php     # optional partial for KPIs
```

Your exact filenames/namespaces may differ, but the **patterns** should match:
- Dashboard request goes through `public/index.php` → `Router` → `Admin\DashboardController` → `View`.
- Dashboard view extends your main layout and uses partials for navigation/flash messages.

---

## Step 1) Copy your base project into `projects/fp`

From the repository root, copy your best, most complete CMS project into the final project folder:

```bash
cp -r projects/08 projects/fp
```

If your strongest project is `projects/07` or `projects/06`, copy that instead and then add any missing pieces from later projects (auth, roles, engagement models, etc.) until it matches your current framework.

Clean up any leftover project‑number branding in the UI (e.g., “Project 07”) so the app looks like a standalone CMS.

---

## Step 2) Add an Admin Dashboard route and controller

Add a **dashboard route** that only admins can access. For example, in `projects/fp/src/Routes/index.php`:

```php
// Admin dashboard (GET)
$router->get('/admin/dashboard', [\App\Controllers\Admin\DashboardController::class, 'index']);
```

Create a new `Admin\DashboardController` (e.g., `projects/fp/src/Controllers/Admin/DashboardController.php`) that:
- Extends your base `Controller`
- Verifies the current user is authenticated **and** has an `admin` role
- Uses models to gather KPI data and recent activity
- Passes data to the `admin/dashboard` view
  
Update your `AuthController` (or equivalent) login handler so that:
- Successful login for an **admin** user redirects to `/admin/dashboard`
- Successful login for a non‑admin user continues to use your existing default (e.g., profile or home page)

Example (simplified) responsibilities:
- `index()`:
  - Authorize admin user
  - Build an array of KPIs (counts, averages, most active user)
  - Load lists for recent contact messages and other recent activity (e.g., new users or posts)
  - Render `admin/dashboard` with all data

---

## Step 3) Define KPI queries and model helper methods

Translate dashboard KPIs into **model‑based queries** following the **"thin controllers, fat models"** principle. **Do not write raw SQL directly in controllers or views.**

### Required Model Helper Methods

Your models should include the following helper methods to encapsulate database logic:

#### Post Model (or Article Model)

Add these analytics methods to your `Post` model:

```php
/**
 * Count featured posts.
 */
public static function countFeatured(): int
{
    $sql = 'SELECT COUNT(*) FROM `' . static::table() . '` WHERE `is_featured` = 1';
    $stmt = static::pdo()->query($sql);
    return (int) $stmt->fetchColumn();
}

/**
 * Calculate average likes per post.
 */
public static function averageLikes(): float
{
    $sql = 'SELECT COALESCE(ROUND(AVG(likes), 2), 0) FROM `' . static::table() . '`';
    $stmt = static::pdo()->query($sql);
    return (float) $stmt->fetchColumn();
}

/**
 * Calculate average favorites per post.
 */
public static function averageFavs(): float
{
    $sql = 'SELECT COALESCE(ROUND(AVG(favs), 2), 0) FROM `' . static::table() . '`';
    $stmt = static::pdo()->query($sql);
    return (float) $stmt->fetchColumn();
}

/**
 * Calculate average comments per post.
 */
public static function averageComments(): float
{
    $sql = 'SELECT COALESCE(ROUND(AVG(comments_count), 2), 0) FROM `' . static::table() . '`';
    $stmt = static::pdo()->query($sql);
    return (float) $stmt->fetchColumn();
}
```

#### User Model

Add this method to count users by role:

```php
/**
 * Count users by role.
 */
public static function countByRole(string $role): int
{
    $sql = 'SELECT COUNT(*) FROM `' . static::table() . '` WHERE `role` = :role';
    $stmt = static::pdo()->prepare($sql);
    $stmt->bindValue(':role', $role);
    $stmt->execute();
    return (int) $stmt->fetchColumn();
}
```

#### PostLike Model

Add these helper methods for like operations:

```php
/**
 * Check if a user has liked a post.
 */
public static function existsForUser(int $postId, int $userId): bool
{
    $pdo = static::pdo();
    $stmt = $pdo->prepare('SELECT 1 FROM `post_likes` WHERE `post_id` = :post_id AND `user_id` = :user_id LIMIT 1');
    $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return (bool)$stmt->fetchColumn();
}

/**
 * Remove a user's like from a post. Returns true if a row was deleted.
 */
public static function deleteForUser(int $postId, int $userId): bool
{
    $pdo = static::pdo();
    $stmt = $pdo->prepare('DELETE FROM `post_likes` WHERE `post_id` = :post_id AND `user_id` = :user_id');
    $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

/**
 * Get all published posts liked by a user.
 */
public static function postsLikedByUser(int $userId): array
{
    $pdo = static::pdo();
    $stmt = $pdo->prepare("
        SELECT p.*
        FROM `post_likes` pl
        JOIN `posts` p ON p.id = pl.post_id
        WHERE pl.user_id = :user_id
          AND p.status = 'published'
        ORDER BY p.published_at DESC
    ");
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
```

#### PostFavorite Model

Add these helper methods for favorite operations:

```php
/**
 * Check if a user has favorited a post.
 */
public static function existsForUser(int $postId, int $userId): bool
{
    $pdo = static::pdo();
    $stmt = $pdo->prepare('SELECT 1 FROM `post_favorites` WHERE `post_id` = :post_id AND `user_id` = :user_id LIMIT 1');
    $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return (bool)$stmt->fetchColumn();
}

/**
 * Remove a user's favorite from a post. Returns true if a row was deleted.
 */
public static function deleteForUser(int $postId, int $userId): bool
{
    $pdo = static::pdo();
    $stmt = $pdo->prepare('DELETE FROM `post_favorites` WHERE `post_id` = :post_id AND `user_id` = :user_id');
    $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

/**
 * Get all published posts favorited by a user.
 */
public static function postsFavoritedByUser(int $userId): array
{
    $pdo = static::pdo();
    $stmt = $pdo->prepare("
        SELECT p.*
        FROM `post_favorites` pf
        JOIN `posts` p ON p.id = pf.post_id
        WHERE pf.user_id = :user_id
          AND p.status = 'published'
        ORDER BY p.published_at DESC
    ");
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
```

#### Comment Model

Add these helper methods for comment operations:

```php
/**
 * Get all published comments for a post.
 */
public static function publishedForPost(int $postId): array
{
    $pdo = static::pdo();
    $stmt = $pdo->prepare("
        SELECT c.*, u.name AS author_name
        FROM `comments` c
        JOIN `users` u ON u.id = c.user_id
        WHERE c.post_id = :post_id AND c.status = 'published'
        ORDER BY c.created_at ASC
    ");
    $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Get distinct posts a user has commented on.
 */
public static function postsCommentedByUser(int $userId): array
{
    $pdo = static::pdo();
    $stmt = $pdo->prepare("
        SELECT DISTINCT p.*
        FROM `comments` c
        JOIN `posts` p ON p.id = c.post_id
        WHERE c.user_id = :user_id
          AND c.status = 'published'
          AND p.status = 'published'
        ORDER BY p.published_at DESC
    ");
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Get all comments with post and user details for admin management.
 */
public static function allWithDetails(?string $statusFilter = null, int $limit = 200): array
{
    $pdo = static::pdo();
    $sql = "
        SELECT c.*, p.title AS post_title, p.slug AS post_slug, u.name AS author_name
        FROM `comments` c
        JOIN `posts` p ON p.id = c.post_id
        JOIN `users` u ON u.id = c.user_id
    ";
    if ($statusFilter) {
        $sql .= " WHERE c.status = :status";
    }
    $sql .= " ORDER BY c.created_at DESC LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    if ($statusFilter) {
        $stmt->bindValue(':status', $statusFilter);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
```

### Dashboard Controller Usage

With these model methods in place, your `DashboardController` should **never contain raw SQL**. Here's an example of the `buildKpis()` method using model methods:

```php
private function buildKpis(): array
{
    $pdo = Database::pdo();

    // Posts - use model methods
    $totalPosts = Post::count();
    $draftPosts = Post::countByStatus('draft');
    $publishedPosts = Post::countByStatus('published');
    $featuredPosts = Post::countFeatured();

    // Users - use model methods
    $totalUsers = User::countByRole('user');
    $totalAdmins = User::countByRole('admin');

    // Contact messages
    $totalContacts = Contact::count();

    // Engagement aggregates - use model methods
    $avgLikes = Post::averageLikes();
    $avgFavs = Post::averageFavs();
    $avgComments = Post::averageComments();

    // Complex analytics query (dashboard-specific, OK to keep in controller)
    $totalInteractions = (int)$pdo->query("
        SELECT
            (SELECT COUNT(*) FROM post_likes) +
            (SELECT COUNT(*) FROM post_favorites) +
            (SELECT COUNT(*) FROM comments)
    ")->fetchColumn();

    $mostActiveUser = $this->mostActiveUser();

    return [
        'total_posts' => $totalPosts,
        'draft_posts' => $draftPosts,
        'published_posts' => $publishedPosts,
        'featured_posts' => $featuredPosts,
        'total_users' => $totalUsers,
        'total_admins' => $totalAdmins,
        'total_contacts' => $totalContacts,
        'average_likes_per_post' => $avgLikes,
        'average_favs_per_post' => $avgFavs,
        'average_comments_per_post' => $avgComments,
        'total_interactions' => $totalInteractions,
        'most_active_user' => $mostActiveUser,
    ];
}
```

**Important principles:**
- Keep all reusable SQL queries in models
- Controllers should only call model methods, not write SQL
- Complex analytics queries specific to the dashboard may remain in the controller
- Use prepared statements for all parameterized queries
- Return clean, view‑friendly data types (no PDOStatement objects in views)

---

## Step 4) Build the Admin Dashboard view

Create `projects/fp/src/Views/admin/dashboard.php` using your templating engine from Project 05:
- Extend your main layout (e.g., `$this->layout('layouts/main');`)
- Use partials for nav and flash messages
- Render KPI cards and recent activity inside your existing Bulma‑style or custom layout.

Dashboard layout should include:
- A **KPI section** with cards showing counts/averages for posts, contact messages, and users
- A **Recent Activity** section (recent contact messages and optionally recent posts/users)

Example structure (pseudocode):

```php
<?php $this->layout('layouts/main'); ?>

<?php $this->start('content'); ?>
  <section class="section">
    <h1 class="title">Admin Dashboard</h1>

    <?php $this->insert('partials/flash'); ?>

    <!-- KPI cards -->
    <div class="columns is-multiline">
      <!-- loop over $kpis and render cards -->
    </div>

    <!-- Recent contact messages / activity -->
    <div class="box">
      <!-- table of recent contact messages -->
    </div>
  </section>
<?php $this->end(); ?>
```

Use `$this->e()` for all dynamic output, and include CSRF tokens in any forms.

---

## Step 5) Display recent contact messages and activity

Add a “Recent Contact Messages” panel or table to the dashboard:
- Query your `ContactMessage` (or equivalent) model for the last 5–10 messages.
- Display key fields: name, email, subject, created_at, short preview of message.
- Link each row to a more detailed view if your app supports it.

Optionally, you may also display:
- Recent comments or user interactions
- A “Most Active User” badge using your engagement tables

All queries should live in models or helpers, not in the view.

---

## Step 7) Update navigation and authorization

Update your main nav partial (e.g., `projects/fp/src/Views/partials/nav.php`) to add an **Admin** menu item that appears only for admins:

```php
<?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
  <a href="/admin/dashboard" class="navbar-item">
    Dashboard
  </a>
<?php endif; ?>
```

Ensure your `DashboardController` (and any admin routes) enforce:
- User is logged in
- User has an admin role
- Non‑admin users are redirected to login or a 403/“not authorized” page with a flash message

Also update your site footer partial (e.g., `projects/fp/src/Views/partials/footer.php`) so it contains appropriate site information (course name, site name, or copyright)
and includes a clear link to the Contact page:

```php
<a href="/contact">Contact Us</a>
```

---

## Requirements Checklist

To receive full credit, your final project must:
- [ ] Start from a working Project 06–08 MVC CMS in `projects/fp/`
- [ ] Route `/admin/dashboard` through your `Router` and `DashboardController`
- [ ] Restrict dashboard access to authenticated admin users only
- [ ] Implement all required model helper methods following the "thin controllers, fat models" principle:
  - [ ] Post analytics methods: `countFeatured()`, `averageLikes()`, `averageFavs()`, `averageComments()`
  - [ ] User role counting: `User::countByRole()`
  - [ ] PostLike methods: `existsForUser()`, `deleteForUser()`, `postsLikedByUser()`
  - [ ] PostFavorite methods: `existsForUser()`, `deleteForUser()`, `postsFavoritedByUser()`
  - [ ] Comment methods: `publishedForPost()`, `postsCommentedByUser()`, `allWithDetails()`
- [ ] **NO raw SQL in controllers** except for complex dashboard-specific analytics queries
- [ ] Controllers use model methods for all standard queries (counts, averages, finding records)
- [ ] Render KPIs in a dashboard view using your view engine and layout/partials
- [ ] Display recent contact messages and/or recent activity in the dashboard
- [ ] Redirect admin users to the dashboard as their default post‑login landing page (instead of a profile page)
- [ ] Update the footer partial with site‑appropriate information and a link to the Contact form at `/contact`
- [ ] Keep all reusable SQL inside models, not in controllers or views
- [ ] Use `$this->e()` for all dynamic output in views
- [ ] Show clear success/error flash messages for admin actions
- [ ] Use prepared statements for all parameterized queries

---

## Quality Assurance & Submission

Before submitting:
- Manually test all dashboard features as both an admin and a regular user
- Verify unauthorized users **cannot** access `/admin/dashboard`
- Confirm KPIs update correctly when data changes (new posts, contact messages, users, etc.)
- Check that forms validate input and show friendly error messages
- Review your code for readability, consistency with previous projects, and security best practices

When you are satisfied:
- Commit and push your `projects/fp` folder to GitHub
- Submit your repository URL (and any specific route, if requested) according to the course instructions

The final project is your chance to demonstrate a **full, integrated MVC application** with a professional‑quality Administrator Dashboard. Treat it like a real client deliverable: cohesive, secure, and polished.

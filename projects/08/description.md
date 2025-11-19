# Project 08 – Post Engagement & Comments
Extend your Project 07 CMS with social features that let authenticated users like, favorite, and comment on blog posts. You will design small join tables, wire up controllers and routes for these actions, and surface engagement counts and comments in the public post view and admin area.

—

## Overview
Starting from a completed Project 07, you will:

1. Add tables for post likes, favorites, and comments
2. Create models and helper methods for engagement data
3. Add routes/controllers for like/unlike and fav/unfav
4. Add routes/controllers for creating and moderating comments (new comments start pending, publish in admin)
5. Update the public post view to show engagement counts and comments
6. Update the admin area to review and manage comments
7. Enhance the existing user profile page with an activity tabs section (Likes, Favs, Comments)

Constraints:
- Keep using your existing MVC structure (Router, Controller, BaseModel, View engine, Validator).
- Require authentication for all like/favorite/comment actions.
- Enforce “one like and one favorite per user per post” with unique constraints and/or validation.
- Protect all state-changing actions with CSRF tokens and role checks where appropriate.

—

## Requirements
- Start from your own, working Project 07 codebase.
- Use the existing `posts.favs`, `posts.likes`, and `posts.comments_count` columns for counters; keep them in sync with the new tables.
- Implement per-user likes and favorites (toggle on/off).
- Implement user comments with validation and basic moderation (new comments default to `pending`; publish via admin).
- Show engagement UI on the public post page and a simple moderation UI in the admin area.

—

## Learning Objectives
- Design and implement one-to-many and many-to-many relationships (posts ↔ users for likes/favorites, posts ↔ comments).
- Use join tables and model helpers to keep counter columns in sync.
- Implement authenticated, CSRF-protected POST actions for likes, favorites, and comments.
- Build simple moderation workflows for user-generated content inside your CMS.

—

## Prerequisites
- Completed Project 07 with:
  - `posts` table including `favs`, `likes`, and `comments_count` numeric fields
  - Public post detail page (by `slug`)
  - Admin posts area (list/create/edit/publish/unpublish/delete)
  - Authenticated users with roles (`admin`, `editor`, `user`)
  - CSRF protection, Validator, View engine, Router, BaseModel

If your Project 07 is incomplete or significantly different, bring it up to date before starting Project 08.

—

## Target Structure

You will extend the existing Project 07 structure. A typical layout might look like:

```
projects/08/
  public/
    index.php
  src/
    Models/
      BaseModel.php
      Post.php              # from P07 (now uses engagement helpers)
      PostLike.php          # new
      PostFavorite.php      # new
      Comment.php           # new
    Controllers/
      HomeController.php
      PostsController.php   # from P07 (now loads comments + engagement)
      PostEngagementController.php   # new (likes/favs)
      CommentsController.php         # new (public comment create/delete)
      Admin/
        PostsController.php          # from P07
        CommentsController.php       # new (moderation)
    Routes/
      index.php             # new routes for likes/favs/comments
    Support/
      Time.php
    Views/
      posts/show.php        # updated with engagement + comments
      partials/
        engagement.php      # optional partial for like/fav UI
      admin/comments/       # new admin screens
        index.php
```

You may organize controllers/views slightly differently, but keep responsibilities clear:
- Public controllers handle user-facing likes/favs/comments on a single post.
- Admin controllers handle reviewing and moderating comments across posts.

—

## Step 1) Copy Project 07 to Project 08

From your course repo root, duplicate your Project 07 app into a new `projects/08` folder so you have a separate codebase for this project:

```bash
cp -r projects/07 projects/08 && \
mkdir -p projects/08/src/Controllers/Admin \
         projects/08/src/Models \
         projects/08/src/Views/admin/comments \
         projects/08/src/Views/partials \
  && \
touch projects/08/src/Controllers/PostEngagementController.php \
      projects/08/src/Controllers/CommentsController.php \
      projects/08/src/Controllers/Admin/CommentsController.php \
      projects/08/src/Models/PostLike.php \
      projects/08/src/Models/PostFavorite.php \
      projects/08/src/Models/Comment.php \
      projects/08/src/Views/partials/engagement.php \
      projects/08/src/Views/admin/comments/index.php
```

Then adjust namespaces or paths if needed so `projects/08/public/index.php` points to the correct `src` directory.

—

## Step 2) Add tables for likes, favorites, and comments

Create three new tables in your database: one for likes, one for favorites, and one for comments. Use integer foreign keys pointing to `posts.id` and `users.id`.

Example schema (adapt to your naming conventions):

```sql
DROP TABLE IF EXISTS post_likes;
DROP TABLE IF EXISTS post_favorites;
DROP TABLE IF EXISTS comments;

CREATE TABLE post_likes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  post_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_post_user_like (post_id, user_id),
  FOREIGN KEY (post_id) REFERENCES posts(id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  INDEX idx_post_likes_post (post_id)
);

CREATE TABLE post_favorites (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  post_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_post_user_fav (post_id, user_id),
  FOREIGN KEY (post_id) REFERENCES posts(id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  INDEX idx_post_favs_post (post_id)
);

CREATE TABLE comments (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  post_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  body TEXT NOT NULL,
  status ENUM('pending','published','deleted') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  INDEX idx_comments_post (post_id),
  INDEX idx_comments_status (status)
);
```

Requirements:
- Each user can like a post at most once (`post_likes` unique index).
- Each user can favorite a post at most once (`post_favorites` unique index).
- Comments reference both the post and the authoring user.
- Use the `status` field to support moderation (`pending`, `published`, `deleted`).

—

## Step 3) Add models and helper methods

Create simple models for likes, favorites, and comments that extend `BaseModel`. Update your `Post` model to include helper methods for engagement counts and lookups.

Example models (namespaces may vary, but follow your Project 07 conventions):

```php
<?php
namespace App\Models;

use PDO;

final class PostLike extends BaseModel
{
    protected static string $table = 'post_likes';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['post_id', 'user_id'];

    /**
     * Check if a like exists for a given post and user.
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
     * Delete a like for a given post and user.
     * Returns true if a row was deleted, false otherwise.
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
}
```

```php
<?php
namespace App\Models;

use PDO;

final class PostFavorite extends BaseModel
{
    protected static string $table = 'post_favorites';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['post_id', 'user_id'];

    /**
     * Check if a favorite exists for a given post and user.
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
     * Delete a favorite for a given post and user.
     * Returns true if a row was deleted, false otherwise.
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
}
```

```php
<?php
namespace App\Models;

use PDO;

final class Comment extends BaseModel
{
    protected static string $table = 'comments';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['post_id', 'user_id', 'body', 'status'];

    /**
     * Get all published comments for a post, with user names.
     */
    public static function publishedForPost(int $postId): array
    {
        $pdo = static::pdo();
        $stmt = $pdo->prepare("
            SELECT c.*, u.name AS user_name
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
     * Get all distinct published posts a user has commented on.
     */
    public static function postsCommentedByUser(int $userId): array
    {
        $pdo = static::pdo();
        $stmt = $pdo->prepare("
            SELECT DISTINCT p.*
            FROM `comments` c
            JOIN `posts` p ON p.id = c.post_id
            WHERE c.user_id = :user_id
              AND c.status <> 'deleted'
              AND p.status = 'published'
            ORDER BY p.published_at DESC
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all comments with post and user details, with optional status filter.
     */
    public static function allWithDetails(?string $statusFilter = null, int $limit = 200): array
    {
        $sql = "
            SELECT c.*, p.title AS post_title, p.slug AS post_slug,
                   u.name AS user_name, u.email AS user_email
            FROM `comments` c
            JOIN `posts` p ON p.id = c.post_id
            JOIN `users` u ON u.id = c.user_id
        ";
        $params = [];
        if ($statusFilter && in_array($statusFilter, ['pending', 'published', 'deleted'], true)) {
            $sql .= " WHERE c.status = :status";
            $params[':status'] = $statusFilter;
        }
        $sql .= " ORDER BY c.created_at DESC LIMIT :limit";

        $pdo = static::pdo();
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
```

In `Post` (from Project 07), add helpers such as:
- `Post::withEngagementBySlug(string $slug, ?int $userId)` – returns a post row with:
  - `likes`, `favs`, `comments_count` from the `posts` table
  - flags like `is_liked_by_user` and `is_favorited_by_user` (via subqueries or separate queries)
- `Post::incrementLikes(int $postId)` / `Post::decrementLikes(int $postId)`
- `Post::incrementFavs(int $postId)` / `Post::decrementFavs(int $postId)`
- `Post::incrementComments(int $postId)` / `Post::decrementComments(int $postId)`

**Implementation notes:**
- The model methods above encapsulate all database logic for engagement features
- Controllers should call these model methods instead of writing raw SQL
- Use existing `BaseModel` helpers (`create()`, `update()`, `find()`) where appropriate
- All counter increment/decrement methods use `GREATEST(column - 1, 0)` to prevent negative values
- Keep controllers thin by moving all SQL queries into model methods

—

## Step 4) Routes and controller for likes and favorites

Add routes for liking/unliking and favoriting/unfavoriting posts. These should be POST routes and require an authenticated user.

`src/Routes/index.php` (example):

```php
use App\Controllers\PostEngagementController;

$router->post('/posts/{id}/like', PostEngagementController::class, 'like');
$router->post('/posts/{id}/unlike', PostEngagementController::class, 'unlike');
$router->post('/posts/{id}/fav', PostEngagementController::class, 'fav');
$router->post('/posts/{id}/unfav', PostEngagementController::class, 'unfav');
```

`PostEngagementController` implementation example:

```php
<?php
namespace App\Controllers;

use App\Controller;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\PostFavorite;

class PostEngagementController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    public function like(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid CSRF token.', 'is-danger');
            $this->redirect('/');
        }

        $post = Post::find($id);
        if (!$post || ($post['status'] ?? '') !== 'published') {
            $this->flash('Post not available.', 'is-warning');
            $this->redirect('/');
        }

        $user = $this->user();
        $userId = (int)($user['id'] ?? 0);

        // Use model method instead of raw SQL
        if (!PostLike::existsForUser($id, $userId)) {
            PostLike::create([
                'post_id' => $id,
                'user_id' => $userId,
            ]);
            Post::incrementLikes($id);
        }

        $this->redirect('/posts/' . $post['slug']);
    }

    public function unlike(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid CSRF token.', 'is-danger');
            $this->redirect('/');
        }

        $post = Post::find($id);
        if (!$post) {
            $this->redirect('/');
        }

        $user = $this->user();
        $userId = (int)($user['id'] ?? 0);

        // Use model method instead of raw SQL
        if (PostLike::deleteForUser($id, $userId)) {
            Post::decrementLikes($id);
        }

        $this->redirect('/posts/' . $post['slug']);
    }

    public function fav(int $id): void
    {
        // Similar to like(), but uses PostFavorite::existsForUser() and Post::incrementFavs()
    }

    public function unfav(int $id): void
    {
        // Similar to unlike(), but uses PostFavorite::deleteForUser() and Post::decrementFavs()
    }
}
```

**Key implementation points:**
- Require authentication in `__construct()` (via `$this->requireAuth()`)
- Validate CSRF tokens on every action
- Look up the post by ID; redirect if not found or not published
- Use model methods (`PostLike::existsForUser()`, `PostLike::deleteForUser()`) instead of raw SQL
- The `like` action is idempotent (calling it multiple times has same effect as once)
- Counters are updated using model methods (`Post::incrementLikes()`, `Post::decrementLikes()`)
- Redirect back to the post detail page after each action

—

## Step 5) Routes and controller for comments (public)

Add routes for creating and deleting comments from the public site. Comment creation should require a logged-in user; deletion of comments should respect ownership/role.

`src/Routes/index.php` (example):

```php
use App\Controllers\CommentsController;

$router->post('/posts/{slug}/comments', CommentsController::class, 'store');
$router->post('/comments/{id}/delete', CommentsController::class, 'destroy');
```

`CommentsController` implementation:
- `store(string $slug)`:
  - Require login and validate CSRF
  - Look up the post by `slug` using `Post::findBySlug($slug)` and ensure it is `published`
  - Validate comment `body` (non-empty, reasonable length)
  - Insert a `Comment` row with `status='pending'` using `Comment::create()`
  - Do **not** increment `posts.comments_count` here; publish handles the counter
  - Redirect back to `/posts/{slug}`
- `destroy(int $id)`:
  - Require login
  - Load the comment using `Comment::find($id)`
  - Allow deletion if:
    - The current user authored the comment, or
    - The current user has `admin` or `editor` role
  - Soft delete by setting `status='deleted'` using `Comment::update()` and, if it was `published`, decrement `posts.comments_count`

**Key points:** Use model methods (`Post::findBySlug()`, `Comment::create()`, `Comment::update()`) instead of raw SQL in the controller.

—

## Step 6) Load engagement and comments in the post detail view

Update your `PostsController@show` action (created in Project 07) to load engagement data and comments for the given post.

Controller implementation (`PostsController@show`):

```php
public function show(string $slug): void
{
    $user = $this->user();
    $userId = $user ? (int)$user['id'] : null;

    // Load post with engagement data
    $post = Post::findBySlugWithAuthorAndEngagement($slug, $userId);
    if (!$post || $post['status'] !== 'published') {
        http_response_code(404);
        echo 'Post not found';
        return;
    }

    // Load published comments using model method
    $comments = Comment::publishedForPost((int)$post['id']);

    // Add human-readable timestamps
    foreach ($comments as &$c) {
        $c['created_human'] = Time::ago($c['created_at']);
    }

    $this->render('posts/show', [
        'title' => $post['title'],
        'post' => $post,
        'published_human' => Time::ago($post['published_at']),
        'comments' => $comments,
    ]);
}
```

**Key points:**
- Use `Post::findBySlugWithAuthorAndEngagement()` to load post with engagement flags
- Use `Comment::publishedForPost()` instead of raw SQL to load comments
- The model method handles the JOIN and filtering, keeping the controller thin

View responsibilities (`src/Views/posts/show.php`):
- Show current engagement counts (Likes, Favorites, Comments) near the title or metadata.
- Show buttons/forms for:
  - Like / Unlike
  - Fav / Unfav
  - These should:
    - Be visible only to logged-in users.
    - Use small forms with `method="post"` and a hidden CSRF field.
    - Point to the routes you added in Step 4.
- Show the list of comments below the post:
  - Display commenter name, time-ago, and escaped comment body.
  - Clearly handle the “no comments yet” state.
- Show a “Add a comment” form for logged-in users:
  - Include a textarea, CSRF field, and submit button.
  - If the user is not logged in, show a link to log in instead of the form.

You may extract the like/fav UI into a partial (for example, `Views/partials/engagement.php`) if you want to reuse it elsewhere.

—

## Step 7) Admin comments moderation

Add a basic comments moderation section to the admin area so `admin` and `editor` roles can review and manage comments.

Routes (`src/Routes/index.php` example):

```php
use App\Controllers\Admin\CommentsController as AdminCommentsController;

$router->get('/admin/comments', AdminCommentsController::class, 'index');
$router->post('/admin/comments/{id}/publish', AdminCommentsController::class, 'publish');
$router->post('/admin/comments/{id}/delete', AdminCommentsController::class, 'destroy');
```

`Admin\CommentsController` implementation example:

```php
<?php
namespace App\Controllers\Admin;

use App\Controller;
use App\Models\Comment;
use App\Models\Post;

class CommentsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireRole('admin', 'editor');
    }

    public function index(): void
    {
        $status = $_GET['status'] ?? null;

        // Use model method instead of raw SQL
        $comments = Comment::allWithDetails($status);

        $this->render('admin/comments/index', [
            'title' => 'Manage Comments',
            'comments' => $comments,
            'status' => $status,
        ]);
    }

    public function publish(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid CSRF token.', 'is-danger');
            $this->redirect('/admin/comments');
        }

        $comment = Comment::find($id);
        if ($comment && $comment['status'] !== 'published') {
            Comment::update($id, ['status' => 'published']);
            Post::incrementComments((int)$comment['post_id']);
        }

        $this->redirect('/admin/comments');
    }

    public function destroy(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid CSRF token.', 'is-danger');
            $this->redirect('/admin/comments');
        }

        $comment = Comment::find($id);
        if ($comment && $comment['status'] !== 'deleted') {
            Comment::update($id, ['status' => 'deleted']);
            if ($comment['status'] === 'published') {
                Post::decrementComments((int)$comment['post_id']);
            }
        }

        $this->redirect('/admin/comments');
    }
}
```

**Key points:**
- Use `Comment::allWithDetails($statusFilter)` to load comments with post and user info
- The model method handles the complex JOIN query and optional filtering
- Counter updates use `Post::incrementComments()` and `Post::decrementComments()`
- Only increment counter when publishing (pending → published)
- Only decrement counter when deleting a previously published comment

Views (`src/Views/admin/comments/*.php`):
- `index.php`:
  - A table of comments with filters and action buttons (Publish/Delete).
  - Links to the related post detail page.

Update your nav partial (`src/Views/partials/nav.php`) to include a link to `/admin/comments` for `admin` and `editor` roles.

—

## Step 8) Keep counters in sync

You are responsible for keeping `posts.likes`, `posts.favs`, and `posts.comments_count` consistent with the data in `post_likes`, `post_favorites`, and `comments`.

Guidelines:
- When you insert a like/favorite, increment the corresponding counter.
- When you remove or soft-delete a like/favorite, decrement the counter (down to a minimum of 0).
- For comments, increment `comments_count` when publishing (pending → published) and decrement only when deleting a published comment. Keep this consistent across user and admin flows.
- If counters ever get out of sync, you should be able to recompute them with a one-off SQL query or script; think about what that query would look like.

—

## Step 9) UX and security polish

User experience:
- Make it obvious whether the current user has already liked/favorited the post:
  - Toggle button text (e.g., “Like” vs “Unlike”).
  - Optionally use different button styles for active states.
- Show friendly messages for states like:
  - “Be the first to like this post.”
  - “No comments yet – add the first comment.”

Security:
- Require login for all engagement actions and comments.
- Include CSRF tokens in all forms (likes, favorites, comments, moderation actions).
- Escape all user content (comment bodies, names) with `$this->e()` to avoid XSS.
- Enforce authorization rules:
  - Only `admin`/`editor` can access `/admin/comments`.
  - Only comment authors or `admin`/`editor` can delete comments from the public side.

—

## Step 10) Profile engagement tabs (Bulma)

Append a Bulma `tabs` section to the bottom of the profile page (`src/Views/profile/show.php`) that shows posts the current user has liked, favorited, and commented on. This builds on the Profile feature you implemented in Project 06 and carried forward into Projects 07–08.

Controller implementation (`ProfileController@show`):

```php
public function show(): void
{
    $this->requireAuth();
    $user = $this->user();
    $userId = (int)($user['id'] ?? 0);

    // Use model methods to load engagement data
    $likedPosts = PostLike::postsLikedByUser($userId);
    $favoritedPosts = PostFavorite::postsFavoritedByUser($userId);
    $commentedPosts = Comment::postsCommentedByUser($userId);

    $this->render('profile/show', [
        'title' => 'Your Profile',
        'user' => $user,
        'likedPosts' => $likedPosts,
        'favoritedPosts' => $favoritedPosts,
        'commentedPosts' => $commentedPosts,
    ]);
}
```

**Key points:**
- Use `PostLike::postsLikedByUser()` instead of raw SQL
- Use `PostFavorite::postsFavoritedByUser()` instead of raw SQL
- Use `Comment::postsCommentedByUser()` instead of raw SQL
- These model methods encapsulate the JOIN queries and filtering logic
- Controller remains thin and focused on orchestration

View updates (`src/Views/profile/show.php`):
- At the bottom of the page (after the existing profile box and buttons), add a Bulma tabs component wrapped in a simple container. BulmaJS is already included in the head partial and will handle the default Bulma tab behavior when you use the standard markup and `data-bulma="tabs"` attribute:

```php
<hr>
  <style>
      .tabs-content li {
          display: none;
          list-style: none;
      }
      .tabs-content li.is-active {
          display: block;
      }
  </style>
  <h2 class="title is-5">Your Activity</h2>
  <div class="tabs-wrapper">
      <div class="tabs is-boxed">
          <ul>
              <li class="is-active">
                  <a>Likes</a>
              </li>
              <li>
                  <a>Favs</a>
              </li>
              <li>
                  <a>Comments</a>
              </li>
          </ul>
      </div>
      <div class="tabs-content">
          <ul>
              <li class="is-active">
                  <?php if (empty($likedPosts)): ?>
                      <p class="has-text-grey">You haven't liked any posts yet.</p>
                  <?php else: ?>
                      <?php foreach ($likedPosts as $post): ?>
                          <p>
                              <a href="/posts/<?= $this->e($post['slug']) ?>">
                                  <?= $this->e($post['title']) ?>
                              </a>
                          </p>
                      <?php endforeach; ?>
                  <?php endif; ?>
              </li>
              <li>
                  <?php if (empty($favoritedPosts)): ?>
                      <p class="has-text-grey">You haven't favorited any posts yet.</p>
                  <?php else: ?>
                      <?php foreach ($favoritedPosts as $post): ?>
                          <p>
                              <a href="/posts/<?= $this->e($post['slug']) ?>"><?= $this->e($post['title']) ?></a>
                          </p>
                      <?php endforeach; ?>
                  <?php endif; ?>
              </li>
              <li>
                  <?php if (empty($commentedPosts)): ?>
                      <p class="has-text-grey">You haven't commented on any posts yet.</p>
                  <?php else: ?>
                      <?php foreach ($commentedPosts as $post): ?>
                          <p>
                              <a href="/posts/<?= $this->e($post['slug']) ?>"><?= $this->e($post['title']) ?></a>
                          </p>
                      <?php endforeach; ?>
                  <?php endif; ?>
              </li>
          </ul>
      </div>
  </div>
```

Notes:
- Keep all dynamic output escaped with `$this->e()`.
- For commented posts, you may choose to show only posts with at least one non-deleted comment by the user.

—

## Rubric (100 points)

- Database & Models (20)
  - Tables for `post_likes`, `post_favorites`, and `comments` created with appropriate constraints (10)
  - `PostLike`, `PostFavorite`, and `Comment` models implemented and integrated with `Post` helpers (10)
- Likes & Favorites (25)
  - Authenticated users can like/unlike posts; likes are stored per user and update `posts.likes` (10)
  - Authenticated users can fav/unfav posts; favorites are stored per user and update `posts.favs` (10)
  - UI clearly shows current counts and user’s like/fav state (5)
- Comments (25)
  - Authenticated users can add comments to posts (with validation and CSRF) (10)
  - Comments appear on the post page in a clear, readable layout (10)
  - Users and/or admins can delete comments; `posts.comments_count` stays in sync (5)
- Admin Moderation (15)
  - Admin comments index shows recent comments with basic info and actions (10)
  - Only `admin`/`editor` can access moderation routes and perform publish/delete actions (5)
- Profile Activity Tabs (5)
  - Profile page shows Bulma tabs listing liked, favorited, and commented posts for the current user (5)
- Code Quality & UX (10)
  - Follows MVC patterns, keeps controllers thin, uses models for DB logic, uses `$this->e()` for output, and provides a reasonable user experience (10)

—

By the end of Project 08, your CMS should feel more like a real blog platform: users can express preference through likes and favorites, contribute comments, and admins can moderate user-generated content while keeping data consistent and secure.

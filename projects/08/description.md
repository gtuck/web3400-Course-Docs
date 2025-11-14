# Project 08 – Post Engagement & Comments
Extend your Project 07 CMS with social features that let authenticated users like, favorite, and comment on blog posts. You will design small join tables, wire up controllers and routes for these actions, and surface engagement counts and comments in the public post view and admin area.

—

## Overview
Starting from a completed Project 07, you will:

1. Add tables for post likes, favorites, and comments
2. Create models and helper methods for engagement data
3. Add routes/controllers for like/unlike and fav/unfav
4. Add routes/controllers for creating and moderating comments
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
- Implement user comments with validation and basic moderation.
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

final class PostLike extends BaseModel
{
    protected static string $table = 'post_likes';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['post_id', 'user_id'];
}
```

```php
<?php
namespace App\Models;

final class PostFavorite extends BaseModel
{
    protected static string $table = 'post_favorites';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['post_id', 'user_id'];
}
```

```php
<?php
namespace App\Models;

final class Comment extends BaseModel
{
    protected static string $table = 'comments';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['post_id', 'user_id', 'body', 'status'];
}
```

In `Post` (from Project 07), add helpers such as:
- `Post::withEngagementBySlug(string $slug, ?int $userId)` – returns a post row with:
  - `likes`, `favs`, `comments_count` from the `posts` table
  - flags like `is_liked_by_user` and `is_favorited_by_user` (via subqueries or separate queries)
- `Post::incrementLikes(int $postId)` / `Post::decrementLikes(int $postId)`
- `Post::incrementFavs(int $postId)` / `Post::decrementFavs(int $postId)`
- `Post::incrementComments(int $postId)` / `Post::decrementComments(int $postId)`

Implementation tips:
- Use existing `BaseModel` helpers wherever possible instead of writing raw PDO in controllers:
  - `create()` / `update()` for inserts and updates
  - `find()` / `firstBy()` for record lookup
  - `existsBy()` for uniqueness checks
- The SQL snippets below show what each helper _does_ in the database; your PHP should call model methods rather than re-implement these queries in controllers.

Example SQL for these helpers (reference only):

```sql
-- Post::withEngagementBySlug($slug, $userId)
SELECT p.*, u.name AS author_name
FROM posts p
JOIN users u ON u.id = p.author_id
WHERE p.slug = :slug
LIMIT 1;
```

```sql
-- Check if current user has liked the post
SELECT 1
FROM post_likes
WHERE post_id = :post_id
  AND user_id = :user_id
LIMIT 1;
```

```sql
-- Check if current user has favorited the post
SELECT 1
FROM post_favorites
WHERE post_id = :post_id
  AND user_id = :user_id
LIMIT 1;
```

```sql
-- Post::incrementLikes($postId)
UPDATE posts
SET likes = likes + 1
WHERE id = :post_id;
```

```sql
-- Post::decrementLikes($postId)
UPDATE posts
SET likes = GREATEST(likes - 1, 0)
WHERE id = :post_id;
```

```sql
-- Post::incrementFavs($postId)
UPDATE posts
SET favs = favs + 1
WHERE id = :post_id;
```

```sql
-- Post::decrementFavs($postId)
UPDATE posts
SET favs = GREATEST(favs - 1, 0)
WHERE id = :post_id;
```

```sql
-- Post::incrementComments($postId)
UPDATE posts
SET comments_count = comments_count + 1
WHERE id = :post_id;
```

```sql
-- Post::decrementComments($postId)
UPDATE posts
SET comments_count = GREATEST(comments_count - 1, 0)
WHERE id = :post_id;
```

Use these helpers (or equivalent) so controllers stay thin and all counter logic stays inside models.

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

`PostEngagementController` (key ideas):
- Require a logged-in user in `__construct()` (e.g., `$this->requireLogin();`).
- Validate CSRF tokens on every action.
- Look up the post by ID; if not found or not published, redirect back with an error.
- On `like`:
  - If a `post_likes` row for `(post_id, user_id)` already exists, do nothing (idempotent).
  - Otherwise insert a new `PostLike` row and increment `posts.likes`.
- On `unlike`:
  - If a `post_likes` row exists, delete it and decrement `posts.likes` (down to a minimum of 0).
- On `fav` / `unfav`, mirror the like/unlike behavior, using `PostFavorite` and the `posts.favs` column.

Implementation tip:
- Use `Post::find($id)`, `PostLike::create()`, and `PostLike::firstBy()` / a small helper rather than hand-writing these queries in the controller. The SQL below is reference only.

After each action, redirect back to the post detail page (e.g., `/posts/{slug}`) with a flash message if you like.

—

## Step 5) Routes and controller for comments (public)

Add routes for creating and deleting comments from the public site. Comment creation should require a logged-in user; deletion of comments should respect ownership/role.

`src/Routes/index.php` (example):

```php
use App\Controllers\CommentsController;

$router->post('/posts/{slug}/comments', CommentsController::class, 'store');
$router->post('/comments/{id}/delete', CommentsController::class, 'destroy');
```

`CommentsController` (key ideas):
- `store(string $slug)`:
  - Require login and validate CSRF.
  - Look up the post by `slug` and ensure it is `published`.
  - Validate comment `body` (non-empty, reasonable length).
  - Insert a `Comment` row with `status` set to either:
    - `'pending'` (if you want moderation before visibility), or
    - `'published'` (if you want immediate visibility).
  - Increment `posts.comments_count`.
  - Redirect back to `/posts/{slug}`.
- `destroy(int $id)`:
  - Require login.
  - Load the comment; allow deletion if:
    - The current user authored the comment, or
    - The current user has `admin` or `editor` role.
  - Soft delete by setting `status='deleted'` (recommended) and decrement `posts.comments_count` for that post.

Implementation tip:
- Use `Post::findBySlug($slug)`, `Comment::create()`, and `Comment::update()` from your models. Let the model layer own the SQL; treat the queries below as documentation of what happens in the database.

—

## Step 6) Load engagement and comments in the post detail view

Update your `PostsController@show` action (created in Project 07) to load engagement data and comments for the given post.

Controller responsibilities:
- Accept `slug` as a route parameter.
- Load the current user (if logged in) so you can mark `is_liked_by_user` and `is_favorited_by_user`.
- Fetch the post via a helper like `Post::withEngagementBySlug($slug, $currentUserId)`:
  - If no published post is found, return 404.
- Load comments for that post:
  - Only include comments with `status='published'` (or include `pending` for admins/editors if you prefer).
  - Order newest-first or oldest-first consistently.
- Pass the post, engagement flags, counts, and comments into the view.

Implementation tip:
- You can either call a small helper on `Comment`/`Post` to load comments by `post_id`, or (if you prefer) write this query in `PostsController@show`. Do not duplicate the same SQL in multiple places—keep it in one helper if you reuse it.

Example SQL for loading comments in `PostsController@show` (reference only):

```sql
SELECT c.*, u.name AS user_name
FROM comments c
JOIN users u ON u.id = c.user_id
WHERE c.post_id = :post_id
  AND c.status = 'published'
ORDER BY c.created_at ASC;
```

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

`Admin\CommentsController`:
- Require role `admin` or `editor` in `__construct()`.
- `index()`:
  - List recent comments with filters (e.g., all, pending, published, deleted).
  - Show columns such as Post title (link), commenter name/email, status, created date, and a small excerpt.
- `publish(int $id)`:
  - Set `status='published'`.
  - Optionally increment `posts.comments_count` if you only count published comments.
- `destroy(int $id)`:
  - Soft delete the comment (`status='deleted'`).
  - Decrement `posts.comments_count` if you only count published comments.

Implementation tip:
- Prefer to keep these queries inside your `Comment` or `Post` models if you reuse them; controllers should call model helpers instead of copying SQL.

Example SQL used by this controller (reference only):

```sql
-- List comments with optional status filter
SELECT c.*, p.title AS post_title, p.slug AS post_slug, u.name AS user_name, u.email AS user_email
FROM comments c
JOIN posts p ON p.id = c.post_id
JOIN users u ON u.id = c.user_id
-- Optional WHERE c.status = :status
ORDER BY c.created_at DESC
LIMIT 200;
```

Views (`src/Views/admin/comments/*.php`):
- `index.php`:
  - A table of comments with filters and action buttons (Publish/Delete).
  - Links to the related post detail page.

Update your nav partial (`src/Views/partials/nav.php`) to include a link to `/admin/comments` for `admin` and `editor` roles.

—

## Step 8) Keep counters in sync

You are responsible for keeping `posts.likes`, `posts.favs`, and `posts.comments_count` consistent with the data in `post_likes`, `post_favorites`, and `comments`.

Guidelines:
- When you insert a like/favorite/comment, increment the corresponding counter.
- When you remove or soft-delete a like/favorite/comment, decrement the counter (down to a minimum of 0).
- When you change a comment’s status (e.g., from `pending` to `published`), decide whether that should affect `comments_count` and keep the behavior consistent.
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

Controller updates (`ProfileController@show`):
- Load the current user ID (e.g., `$userId = (int)($this->user()['id'] ?? 0);`).
- Use your engagement models to fetch three lists:
  - `$likedPosts` – posts the user has liked (join `post_likes` → `posts`).
  - `$favoritedPosts` – posts the user has favorited (join `post_favorites` → `posts`).
  - `$commentedPosts` – distinct posts the user has commented on (join `comments` → `posts`, filter out `status='deleted'`).
- Pass these arrays into the view in addition to the existing `$user` data.

Implementation tip:
- These joins can live in a small helper method on a new model (e.g., `PostLike::forUser($userId)`) or in `ProfileController@show`. If you use them in multiple places, move them into a model to avoid duplication.

Example SQL for these profile queries (reference only):

```sql
-- Posts the user has liked
SELECT p.*
FROM post_likes pl
JOIN posts p ON p.id = pl.post_id
WHERE pl.user_id = :user_id
  AND p.status = 'published'
ORDER BY p.published_at DESC;
```

```sql
-- Posts the user has favorited
SELECT p.*
FROM post_favorites pf
JOIN posts p ON p.id = pf.post_id
WHERE pf.user_id = :user_id
  AND p.status = 'published'
ORDER BY p.published_at DESC;
```

```sql
-- Posts the user has commented on (distinct)
SELECT DISTINCT p.*
FROM comments c
JOIN posts p ON p.id = c.post_id
WHERE c.user_id = :user_id
  AND c.status <> 'deleted'
  AND p.status = 'published'
ORDER BY p.published_at DESC;
```

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
                              <a href="/posts<?= $user ? '/' . $this->e($post['slug']) : '' ?>">
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

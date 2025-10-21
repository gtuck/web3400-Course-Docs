# Project 06 – Authentication & Authorization (Users + Roles)
Build user accounts with secure registration, login/logout, sessions, role‑based authorization, and an admin users panel. Keep the MVC fundamentals front‑and‑center and use your existing framework from Project 05 (templating, CSRF, validator, RESTful routing, custom exceptions).

—

## Overview
Starting from a completed Project 05, you will:

1. Create a `users` table and a `User` model
2. Implement secure registration (validation, unique email, password hashing)
3. Implement login/logout with session management and CSRF protection
4. Add role‑based authorization (`admin`, `editor`, `user`)
5. Add user profile management (view/edit profile, change password)
6. Build an admin‑only users area (list, create, edit, change role, deactivate/delete)
7. Integrate roles into navigation and protect routes/views accordingly

Constraints:
- Vanilla PHP only — use only your framework from earlier projects plus core PHP (sessions, `password_hash`, `password_verify`).
- Use existing `BaseModel.php` for CRUD. If a query can’t be expressed with the existing methods, add a small, reusable helper to `BaseModel` (do not bypass the model in controllers).
- Favor simple GET/POST endpoints for admin operations.

—

## Learning Objectives
- Implement session‑based authentication in vanilla PHP
- Hash and verify passwords securely (`password_hash`/`password_verify`)
- Enforce CSRF protection on all state‑changing actions
- Use role checks to protect routes and views (authorization)
- Keep controllers thin: validation in `Validator`, data access in `Models`
- Extend a shared base to add small, composable features (e.g., minimal BaseModel helpers)

—

## Prerequisites
- Completed Project 05 with:
  - `src/Support/View.php`, `src/Support/Validator.php`
  - `src/Controller.php` (shares `csrfToken`, has `validateCsrf`)
  - Router + basic views/partials
- Working database connection via `Support/Database.php`

—

## Target Structure

```
projects/06/
  public/
    index.php                # same front controller (sessions + Dotenv)
  src/
    Controller.php           # add lightweight auth helpers (see below)
    Support/
      View.php               # add csrfField() helper for forms
    Models/
      BaseModel.php          # small generic find helpers (see below)
      User.php               # new model
    Controllers/
      AuthController.php     # register/login/logout
      ProfileController.php  # view/edit profile, change password
      Admin/
        UsersController.php  # admin‑only: list/create/edit/role/deactivate(delete)
    Routes/
      index.php              # add routes for auth, profile, admin users
    Views/
      auth/{login.php,register.php}
      profile/{show.php,edit.php,change_password.php}
      admin/users/{index.php,create.php,edit.php}
      partials/nav.php       # integrate role‑aware navigation
```

—

## Step 1) Create the `users` table

Keep it simple and portable. Example MySQL schema:

```sql
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'user', -- 'admin', 'editor', 'user'
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_email (email)
);
```

Seed at least one admin. Options:
- Temporarily register a user, then update `role='admin'` in DB; or
- Insert with a precomputed hash:

```sql
-- Generate the hash in PHP: password_hash('YourAdminPass', PASSWORD_DEFAULT)
INSERT INTO users (name, email, password_hash, role) VALUES
('Admin', 'admin@example.com', '$2y$...yourhash...', 'admin');
```

—

## Step 2) Add a `User` model

`src/Models/User.php`
```php
<?php
namespace App\Models;

class User extends BaseModel
{
    protected static string $table = 'users';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'name', 'email', 'password_hash', 'role', 'is_active',
    ];
}
```

—

## Step 3) Small generic helpers in `BaseModel`

To support lookups like “find user by email” or “does an email exist?”, add minimal reusable helpers. Keep them generic so other models can use them.

`src/Models/BaseModel.php` (additions)
```php
// Find the first row where `$column = $value` or return null
public static function firstBy(string $column, mixed $value): ?array
{
    $sql = 'SELECT * FROM `'. static::table() .'` WHERE `'. $column .'` = :v LIMIT 1';
    $stmt = static::pdo()->prepare($sql);
    $stmt->bindValue(':v', $value);
    $stmt->execute();
    $row = $stmt->fetch();
    return $row ?: null;
}

// Check if any row exists for `$column = $value`, with optional id exclusion
public static function existsBy(string $column, mixed $value, ?int $exceptId = null): bool
{
    $sql = 'SELECT COUNT(*) FROM `'. static::table() .'` WHERE `'. $column .'` = :v';
    if ($exceptId !== null) {
        $sql .= ' AND `'. static::pk() .'` <> :id';
    }
    $stmt = static::pdo()->prepare($sql);
    $stmt->bindValue(':v', $value);
    if ($exceptId !== null) {
        $stmt->bindValue(':id', $exceptId);
    }
    $stmt->execute();
    return (bool) $stmt->fetchColumn();
}
```

—

## Step 4) Add lightweight auth helpers to your base `Controller`

Add these helpers to keep controllers tidy and route protection consistent.

`src/Controller.php` (additions)
```php
// Return current user as array (or null). Cache per request.
protected ?array $currentUser = null;

protected function user(): ?array
{
    if ($this->currentUser !== null) {
        return $this->currentUser;
    }
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $id = $_SESSION['user_id'] ?? null;
    if (!$id) {
        return $this->currentUser = null;
    }
    return $this->currentUser = \App\Models\User::find((int)$id);
}

protected function loginUser(array $user): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['user_role'] = $user['role'] ?? 'user';
}

protected function logoutUser(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

protected function requireAuth(): void
{
    if (!$this->user()) {
        $this->flash('Please log in to continue.', 'is-warning');
        $this->redirect('/login');
    }
}

protected function requireRole(string ...$roles): void
{
    $user = $this->user();
    if (!$user || !in_array($user['role'] ?? 'user', $roles, true)) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}
```

—

## Step 5) Add a `csrfField()` helper to the `View`

Convenience helper to echo a hidden CSRF input in any form.

`src/Support/View.php` (additions)
```php
/**
 * Output a CSRF token hidden input field
 *
 * Convenience helper to avoid repeating the same code in every form.
 * Automatically escapes the token value for safety.
 * Use CSRF protection for all state‑changing requests.
 */
public function csrfField(): void
{
    $token = $this->shared['csrfToken'] ?? '';
    echo '<input type="hidden" name="csrf_token" value="' . $this->e($token) . '">';
}
```

—

## Step 6) Implement Registration (GET/POST)

Routes (`src/Routes/index.php`):
- `GET /register` → `AuthController@showRegister`
- `POST /register` → `AuthController@register`

Controller (`src/Controllers/AuthController.php`, excerpts):
```php
public function showRegister(): void
{
    $this->render('auth/register', ['title' => 'Register']);
}

public function register(): void
{
    if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
        $this->flash('Security token validation failed.', 'is-danger');
        return $this->redirect('/register');
    }

    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => strtolower(trim($_POST['email'] ?? '')),
        'password' => $_POST['password'] ?? '',
        'password_confirm' => $_POST['password_confirm'] ?? '',
    ];

    $errors = \App\Support\Validator::validate($data, [
        'name' => 'required|max:255',
        'email' => 'required|email|max:255',
        'password' => 'required|min:8',
    ]);

    if ($data['password'] !== $data['password_confirm']) {
        $errors['password'][] = 'Password confirmation does not match.';
    }

    // Unique email
    if (\App\Models\User::existsBy('email', $data['email'])) {
        $errors['email'][] = 'Email is already registered.';
    }

    if (!empty($errors)) {
        foreach (\App\Support\Validator::flattenErrors($errors) as $m) {
            $this->flash($m, 'is-warning');
        }
        return $this->render('auth/register', ['title' => 'Register', 'old' => $data]);
    }

    $id = \App\Models\User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
        'role' => 'user',
        'is_active' => 1,
    ]);

    $user = \App\Models\User::find($id);
    $this->loginUser($user);
    $this->flash('Welcome, your account has been created!', 'is-success');
    $this->redirect('/profile');
}
```

View (`src/Views/auth/register.php`, excerpt):
```php
<?php $this->layout('layouts/main'); $this->start('content'); ?>
<h1>Register</h1>
<form method="post" action="/register">
  <?php $this->csrfField(); ?>
  <label>Name <input type="text" name="name" value="<?= $this->e($old['name'] ?? '') ?>" required></label>
  <label>Email <input type="email" name="email" value="<?= $this->e($old['email'] ?? '') ?>" required></label>
  <label>Password <input type="password" name="password" required></label>
  <label>Confirm Password <input type="password" name="password_confirm" required></label>
  <button type="submit">Create Account</button>
  <a href="/login">Already have an account?</a>
  </form>
<?php $this->end(); ?>
```

—

## Step 7) Implement Login/Logout (GET/POST)

Routes:
- `GET /login` → `AuthController@showLogin`
- `POST /login` → `AuthController@login`
- `POST /logout` → `AuthController@logout` (CSRF‑protected)

Controller (excerpts):
```php
public function showLogin(): void
{
    $this->render('auth/login', ['title' => 'Login']);
}

public function login(): void
{
    if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
        $this->flash('Security token validation failed.', 'is-danger');
        return $this->redirect('/login');
    }

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $errors = \App\Support\Validator::validate(compact('email','password'), [
        'email' => 'required|email',
        'password' => 'required',
    ]);
    if (!empty($errors)) {
        foreach (\App\Support\Validator::flattenErrors($errors) as $m) {
            $this->flash($m, 'is-warning');
        }
        return $this->redirect('/login');
    }

    $user = \App\Models\User::firstBy('email', $email);
    if (!$user || !$user['is_active']) {
        $this->flash('Invalid credentials.', 'is-danger');
        return $this->redirect('/login');
    }

    if (!password_verify($password, $user['password_hash'])) {
        $this->flash('Invalid credentials.', 'is-danger');
        return $this->redirect('/login');
    }

    $this->loginUser($user);
    $this->flash('Welcome back!', 'is-success');
    $this->redirect('/');
}

public function logout(): void
{
    if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
        $this->flash('Security token validation failed.', 'is-danger');
        return $this->redirect('/');
    }
    $this->logoutUser();
    $this->flash('You have been logged out.', 'is-info');
    $this->redirect('/');
}
```

View (`src/Views/auth/login.php`, excerpt):
```php
<?php $this->layout('layouts/main'); $this->start('content'); ?>
<h1>Login</h1>
<form method="post" action="/login">
  <?php $this->csrfField(); ?>
  <label>Email <input type="email" name="email" required></label>
  <label>Password <input type="password" name="password" required></label>
  <button type="submit">Login</button>
  <a href="/register">Create account</a>
</form>
<?php $this->end(); ?>
```

—

## Step 8) Profile: view/edit + change password

Routes:
- `GET /profile` → `ProfileController@show` (requires auth)
- `GET /profile/edit` → `ProfileController@edit` (requires auth)
- `POST /profile` → `ProfileController@update` (requires CSRF)
- `POST /profile/password` → `ProfileController@changePassword` (requires CSRF)

Controller (excerpts):
```php
public function show(): void
{
    $this->requireAuth();
    $this->render('profile/show', ['title' => 'Your Profile', 'user' => $this->user()]);
}

public function edit(): void
{
    $this->requireAuth();
    $this->render('profile/edit', ['title' => 'Edit Profile', 'user' => $this->user()]);
}

public function update(): void
{
    $this->requireAuth();
    if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
        $this->flash('Security token validation failed.', 'is-danger');
        return $this->redirect('/profile/edit');
    }
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $id = (int)($this->user()['id'] ?? 0);

    $errors = \App\Support\Validator::validate(compact('name','email'), [
        'name' => 'required|max:255',
        'email' => 'required|email|max:255',
    ]);
    if (\App\Models\User::existsBy('email', $email, $id)) {
        $errors['email'][] = 'That email is already in use.';
    }
    if (!empty($errors)) {
        foreach (\App\Support\Validator::flattenErrors($errors) as $m) {
            $this->flash($m, 'is-warning');
        }
        return $this->redirect('/profile/edit');
    }
    \App\Models\User::update($id, compact('name','email'));
    $this->flash('Profile updated.', 'is-success');
    $this->redirect('/profile');
}

public function changePassword(): void
{
    $this->requireAuth();
    if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
        $this->flash('Security token validation failed.', 'is-danger');
        return $this->redirect('/profile');
    }
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['new_password_confirm'] ?? '';
    $user = $this->user();

    if (!password_verify($current, $user['password_hash'])) {
        $this->flash('Current password is incorrect.', 'is-danger');
        return $this->redirect('/profile');
    }
    $errs = \App\Support\Validator::validate(['p' => $new], ['p' => 'required|min:8']);
    if ($new !== $confirm) {
        $errs['p'][] = 'Password confirmation does not match.';
    }
    if (!empty($errs)) {
        foreach (\App\Support\Validator::flattenErrors($errs) as $m) {
            $this->flash($m, 'is-warning');
        }
        return $this->redirect('/profile');
    }
    \App\Models\User::update((int)$user['id'], ['password_hash' => password_hash($new, PASSWORD_DEFAULT)]);
    $this->flash('Password changed.', 'is-success');
    $this->redirect('/profile');
}
```

—

## Step 9) Admin‑only Users Management (GET/POST)

All routes require `admin` role. Keep endpoints simple and POST for state changes.

Routes:
- `GET /admin/users` → list users
- `GET /admin/users/create` → new user form
- `POST /admin/users` → create
- `GET /admin/users/{id}/edit` → edit user form
- `POST /admin/users/{id}` → update name/email/role/active
- `POST /admin/users/{id}/role` → change role
- `POST /admin/users/{id}/deactivate` → deactivate (or `POST /admin/users/{id}/delete` to delete)

Controller guard pattern (in `UsersController` constructor or each action):
```php
public function __construct()
{
    parent::__construct();
    $this->requireRole('admin');
}
```

Examples (excerpts):
```php
public function index(): void
{
    $users = \App\Models\User::all(limit: 200, offset: 0, orderBy: '`id` DESC');
    $this->render('admin/users/index', ['title' => 'Users', 'users' => $users]);
}

public function store(): void
{
    if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
        $this->flash('Security token validation failed.', 'is-danger');
        return $this->redirect('/admin/users/create');
    }
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $role = $_POST['role'] ?? 'user';
    $password = $_POST['password'] ?? '';

    $errors = \App\Support\Validator::validate(
        compact('name','email','role','password'),
        [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'role' => 'required|in:admin,editor,user',
            'password' => 'required|min:8',
        ]
    );
    if (\App\Models\User::existsBy('email', $email)) {
        $errors['email'][] = 'Email is already registered.';
    }
    if (!empty($errors)) {
        foreach (\App\Support\Validator::flattenErrors($errors) as $m) {
            $this->flash($m, 'is-warning');
        }
        return $this->redirect('/admin/users/create');
    }

    \App\Models\User::create([
        'name' => $name,
        'email' => $email,
        'role' => $role,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'is_active' => 1,
    ]);
    $this->flash('User created.', 'is-success');
    $this->redirect('/admin/users');
}

public function update(int $id): void
{
    if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
        $this->flash('Security token validation failed.', 'is-danger');
        return $this->redirect("/admin/users/{$id}/edit");
    }
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $role = $_POST['role'] ?? 'user';
    $is_active = (int)($_POST['is_active'] ?? 1);

    $errors = \App\Support\Validator::validate(
        compact('name','email','role'),
        [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'role' => 'required|in:admin,editor,user',
        ]
    );
    if (\App\Models\User::existsBy('email', $email, $id)) {
        $errors['email'][] = 'That email is already in use.';
    }
    if (!empty($errors)) {
        foreach (\App\Support\Validator::flattenErrors($errors) as $m) {
            $this->flash($m, 'is-warning');
        }
        return $this->redirect("/admin/users/{$id}/edit");
    }

    \App\Models\User::update($id, compact('name','email','role','is_active'));
    $this->flash('User updated.', 'is-success');
    $this->redirect('/admin/users');
}

public function deactivate(int $id): void
{
    if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
        $this->flash('Security token validation failed.', 'is-danger');
        return $this->redirect('/admin/users');
    }
    \App\Models\User::update($id, ['is_active' => 0]);
    $this->flash('User deactivated.', 'is-info');
    $this->redirect('/admin/users');
}
```

—

## Step 10) Routes (favor GET/POST)

`src/Routes/index.php` example additions:
```php
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\Admin\UsersController;

$router->get('/register', AuthController::class, 'showRegister');
$router->post('/register', AuthController::class, 'register');
$router->get('/login', AuthController::class, 'showLogin');
$router->post('/login', AuthController::class, 'login');
$router->post('/logout', AuthController::class, 'logout');

$router->get('/profile', ProfileController::class, 'show');
$router->get('/profile/edit', ProfileController::class, 'edit');
$router->post('/profile', ProfileController::class, 'update');
$router->post('/profile/password', ProfileController::class, 'changePassword');

$router->get('/admin/users', UsersController::class, 'index');
$router->get('/admin/users/create', UsersController::class, 'create');
$router->post('/admin/users', UsersController::class, 'store');
$router->get('/admin/users/{id}/edit', UsersController::class, 'edit');
$router->post('/admin/users/{id}', UsersController::class, 'update');
$router->post('/admin/users/{id}/role', UsersController::class, 'updateRole');
$router->post('/admin/users/{id}/deactivate', UsersController::class, 'deactivate');
```

—

## Step 11) Role‑aware navigation and protected views

Update `src/Views/partials/nav.php` so it reflects auth state and roles:
- Guest: show “Login”, “Register”
- Authenticated: show “Profile”, “Logout” (logout must be a POST form with CSRF)
- Admins: show “Users” link to `/admin/users`

Example (excerpt):
```php
<?php $u = $_SESSION['user_id'] ?? null; $role = $_SESSION['user_role'] ?? 'user'; ?>
<nav>
  <a href="/">Home</a>
  <?php if ($u): ?>
    <a href="/profile">Profile</a>
    <?php if ($role === 'admin'): ?><a href="/admin/users">Users</a><?php endif; ?>
    <form method="post" action="/logout" style="display:inline">
      <?php $this->csrfField(); ?>
      <button type="submit">Logout</button>
    </form>
  <?php else: ?>
    <a href="/login">Login</a>
    <a href="/register">Register</a>
  <?php endif; ?>
</nav>
```

—

## Security Notes
- Always validate CSRF before processing POST requests.
- Hash passwords with `password_hash` (never store plain text).
- Verify with `password_verify` and regenerate session IDs on login (`session_regenerate_id(true)`).
- Check `is_active` on login and in admin tooling; deactivated users should not authenticate.
- Sanitize and validate user input with `Validator` rules; limit updates via `$fillable`.

—

## Tips and Gotchas
- Keep controllers thin: no SQL in controllers — if you need a new query, add a small, reusable helper to `BaseModel`.
- Use `User::firstBy('email', $email)` for lookups and `User::existsBy('email', $email, $exceptId)` for uniqueness checks.
- Favor GET for forms and POST for actions in admin; keep URLs predictable.
- Use PRG (Post‑Redirect‑Get) after successful POSTs to prevent resubmission.
- Reuse the templating engine: `$this->layout()`, `$this->start()/end()`, `$this->insert()` and `$this->e()` everywhere.

—

## Grading Checklist

Core Auth
- [ ] `users` table with unique email, hashed password, role, is_active
- [ ] `User` model extends `BaseModel` with correct `$table`, `$fillable`
- [ ] Registration validates, enforces unique email, hashes passwords
- [ ] Login verifies password, regenerates session ID, respects `is_active`
- [ ] Logout via POST with CSRF protection

Authorization
- [ ] Role checks in controllers (require `admin` for admin area)
- [ ] Navigation reflects auth state and admin role
- [ ] Protected routes redirect unauthenticated users to `/login`

Profile
- [ ] View profile page shows current user info
- [ ] Edit profile updates name/email with validation and uniqueness
- [ ] Change password verifies current password and enforces min length

Admin Users
- [ ] List users page
- [ ] Create user (name/email/role/password) with validation + CSRF
- [ ] Edit user (name/email/role/active) with validation + CSRF
- [ ] Change role endpoint (POST)
- [ ] Deactivate or delete endpoint (POST)

Framework Integration
- [ ] CSRF tokens included in all forms via `$this->csrfField()`
- [ ] Minimal generic helpers added to `BaseModel` (`firstBy`, `existsBy`)
- [ ] No external packages used; only Project 05 tooling + core PHP

—

## Submission
Submit the direct URL to your Project 06 folder in your repository:

```
https://github.com/YOUR-USER/YOUR-REPO/blob/main/projects/06/
```

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
        UsersController.php  # admin‑only: list/create/edit/role/active (toggle)
    Routes/
      index.php              # add routes for auth, profile, admin users
    Views/
      auth/{login.php,register.php}
      profile/{show.php,edit.php,change_password.php}
      admin/users/{index.php,create.php,edit.php}
      partials/nav.php       # integrate role‑aware navigation
```

—

## Step 1) Run the Project 06 setup script

From the repo root run the scaffolding script to create the empty directories and placeholder files you will fill in during this project:

```bash
cp -r projects/05 projects/06
cd projects/06

# Directories
mkdir -p public
mkdir -p src/Controllers/Admin
mkdir -p src/Models
mkdir -p src/Routes
mkdir -p src/Support
mkdir -p src/Views/auth
mkdir -p src/Views/profile
mkdir -p src/Views/admin/users

# Empty files
touch src/Controllers/AuthController.php
touch src/Controllers/ProfileController.php
touch src/Controllers/Admin/UsersController.php
touch src/Models/User.php
touch src/Views/auth/login.php
touch src/Views/auth/register.php
touch src/Views/profile/show.php
touch src/Views/profile/edit.php
touch src/Views/profile/change_password.php
touch src/Views/admin/users/index.php
touch src/Views/admin/users/create.php
touch src/Views/admin/users/edit.php
```
—

## Step 2) Create the `users` table

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
('Admin', 'admin@example.com', '$2y$10$SfhYIDtn.iOuCW7zfoFLuuZHX6lja4lF4XA4JqNmpiH/.P3zB8JCa', 'admin');
```
- Login with: admin@example.com
- Password: test

—

## Step 3) Add a `User` model

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

## Step 4) Small data-layer helpers (BaseModel + Validator)

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

The new validation rules used throughout the project also need support in your shared validator. Extend the existing `match` inside `src/Support/Validator.php::applyRule()` so it understands minimum lengths and constrained sets:

`src/Support/Validator.php` (within `applyRule`)
```php
    return match ($rule) {
        'required' => ($value === null || $value === '') ? ucfirst($field) . " is required." : null,
        'email' => ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) ? ucfirst($field) . " must be a valid email." : null,
        'max' => ($value && mb_strlen($value) > (int)$param) ? ucfirst($field) . " must not exceed {$param} characters." : null,
        'min' => ($value && mb_strlen($value) < (int)$param) ? ucfirst($field) . " must be at least {$param} characters." : null,
        'in' => ($value && !in_array($value, array_map('trim', explode(',', (string)$param)), true))
            ? ucfirst($field) . ' must be one of: ' . $param . '.'
            : null,
        default => null,
    };
```

—

## Step 5) Add lightweight auth helpers to your base `Controller`

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

## Step 6) Add a `csrfField()` helper to the `View`

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

## Step 7) Implement Registration Route (GET/POST), Controller and View

Routes (`src/Routes/index.php`):

```php
use App\Controllers\AuthController; //new line

...

$router->get('/register', AuthController::class, 'showRegister'); // new line
$router->post('/register', AuthController::class, 'register'); // new line
```

Controller (`src/Controllers/AuthController.php`):

```php
<?php
// filepath: projects/06/src/Controllers/AuthController.php
namespace App\Controllers;

use App\Controller;
use App\Models\User;
use App\Support\Validator;

class AuthController extends Controller
{
    public function showRegister(): void
    {
        $this->render('auth/register', ['title' => 'Register']);
    }

    public function register(): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect('/register');
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
            $this->render('auth/register', ['title' => 'Register', 'old' => $data]);
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
}
```

View (`src/Views/auth/register.php`):

```php
<?php $this->layout('layouts/main'); $this->start('content'); ?>
<section class="section">
  <div class="container">
    <h1 class="title">Register</h1>
    <form class="box" method="post" action="/register">
      <?php $this->csrfField(); ?>
      <div class="field">
        <label class="label" for="name">Name</label>
        <div class="control">
          <input class="input" id="name" type="text" name="name" value="<?= $this->e($old['name'] ?? '') ?>" required>
        </div>
      </div>
      <div class="field">
        <label class="label" for="email">Email</label>
        <div class="control">
          <input class="input" id="email" type="email" name="email" value="<?= $this->e($old['email'] ?? '') ?>" required>
        </div>
      </div>
      <div class="field">
        <label class="label" for="password">Password</label>
        <div class="control">
          <input class="input" id="password" type="password" name="password" required>
        </div>
      </div>
      <div class="field">
        <label class="label" for="password_confirm">Confirm Password</label>
        <div class="control">
          <input class="input" id="password_confirm" type="password" name="password_confirm" required>
        </div>
      </div>
      <div class="field is-grouped is-justify-content-space-between is-align-items-center">
        <div class="control">
          <button class="button is-primary" type="submit">Create Account</button>
        </div>
        <div class="control">
          <a class="button is-text" href="/login">Already have an account?</a>
        </div>
      </div>
    </form>
  </div>
</section>
<?php $this->end(); ?>
```

—

## Step 8) Implement Login/Logout Route (GET/POST), Controller and View

Routes (`src/Routes/index.php`):
```php
use App\Controllers\AuthController; // new line

...

$router->get('/login', AuthController::class, 'showLogin'); // new line
$router->post('/login', AuthController::class, 'login'); // new line
$router->post('/logout', AuthController::class, 'logout'); // new line
```

Controller (`src/Controllers/AuthController.php` additions):
```php
public function showLogin(): void
{
    $this->render('auth/login', ['title' => 'Login']);
}

public function login(): void
{
    if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
        $this->flash('Security token validation failed.', 'is-danger');
        $this->redirect('/login');
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
        $this->redirect('/login');
    }

    $user = \App\Models\User::firstBy('email', $email);
    if (!$user || !$user['is_active']) {
        $this->flash('Invalid credentials.', 'is-danger');
        $this->redirect('/login');
    }

    if (!password_verify($password, $user['password_hash'])) {
        $this->flash('Invalid credentials.', 'is-danger');
        $this->redirect('/login');
    }

    $this->loginUser($user);
    $this->flash('Welcome back!', 'is-success');
    $this->redirect('/');
}

public function logout(): void
{
    if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
        $this->flash('Security token validation failed.', 'is-danger');
        $this->redirect('/');
    }
    $this->logoutUser();
    $this->flash('You have been logged out.', 'is-info');
    $this->redirect('/');
}
```

View (`src/Views/auth/login.php`):
```php
<?php $this->layout('layouts/main'); $this->start('content'); ?>
<section class="section">
  <div class="container">
    <h1 class="title">Login</h1>
    <form class="box" method="post" action="/login">
      <?php $this->csrfField(); ?>
      <div class="field">
        <label class="label" for="email">Email</label>
        <div class="control">
          <input class="input" id="email" type="email" name="email" required>
        </div>
      </div>
      <div class="field">
        <label class="label" for="password">Password</label>
        <div class="control">
          <input class="input" id="password" type="password" name="password" required>
        </div>
      </div>
      <div class="field is-grouped is-justify-content-space-between is-align-items-center">
        <div class="control">
          <button class="button is-link" type="submit">Login</button>
        </div>
        <div class="control">
          <a class="button is-text" href="/register">Create account</a>
        </div>
      </div>
    </form>
  </div>
</section>
<?php $this->end(); ?>
```

—

## Step 9) Profile: view/edit + change password

Routes (`src/Routes/index.php`):
```php
use App\Controllers\ProfileController; // new line

...

$router->get('/profile', ProfileController::class, 'show'); // new line
$router->get('/profile/edit', ProfileController::class, 'edit'); // new line
$router->post('/profile', ProfileController::class, 'update'); // new line
$router->post('/profile/password', ProfileController::class, 'changePassword'); // new line
```

Controller (`src/Controllers/ProfileController.php`):
```php
<?php
// filepath: projects/06/src/Controllers/ProfileController.php
namespace App\Controllers;

use App\Controller;
use App\Models\User;
use App\Support\Validator;

class ProfileController extends Controller
{
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
        $this->redirect('/profile/edit');
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
        $this->redirect('/profile/edit');
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
        $this->redirect('/profile');
    }
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['new_password_confirm'] ?? '';
        $user = $this->user();

    if (!password_verify($current, $user['password_hash'])) {
        $this->flash('Current password is incorrect.', 'is-danger');
        $this->redirect('/profile');
    }
        $errs = \App\Support\Validator::validate(['p' => $new], ['p' => 'required|min:8']);
        if ($new !== $confirm) {
            $errs['p'][] = 'Password confirmation does not match.';
        }
    if (!empty($errs)) {
        foreach (\App\Support\Validator::flattenErrors($errs) as $m) {
            $this->flash($m, 'is-warning');
        }
        $this->redirect('/profile');
    }
        \App\Models\User::update((int)$user['id'], ['password_hash' => password_hash($new, PASSWORD_DEFAULT)]);
        $this->flash('Password changed.', 'is-success');
        $this->redirect('/profile');
    }
}
```

Views:

`src/Views/profile/show.php`
```php
<?php $this->layout('layouts/main'); $this->start('content'); ?>
<section class="section">
  <div class="container">
    <h1 class="title">Your Profile</h1>
    <div class="box">
      <p><strong>Name:</strong> <?= $this->e(($user['name'] ?? '') ?: ($this->user()['name'] ?? '')) ?></p>
      <p><strong>Email:</strong> <?= $this->e(($user['email'] ?? '') ?: ($this->user()['email'] ?? '')) ?></p>
    </div>
    <div class="buttons">
      <a class="button is-link" href="/profile/edit">Edit Profile</a>
    </div>
  </div>
  </section>
<?php $this->end(); ?>
```

`src/Views/profile/edit.php`
```php
<?php $this->layout('layouts/main'); $this->start('content'); ?>
<section class="section">
  <div class="container">
    <h1 class="title">Edit Profile</h1>
    <form class="box" method="post" action="/profile">
      <?php $this->csrfField(); ?>
      <div class="field">
        <label class="label" for="name">Name</label>
        <div class="control">
          <input class="input" id="name" type="text" name="name" value="<?= $this->e($user['name'] ?? '') ?>" required>
        </div>
      </div>
      <div class="field">
        <label class="label" for="email">Email</label>
        <div class="control">
          <input class="input" id="email" type="email" name="email" value="<?= $this->e($user['email'] ?? '') ?>" required>
        </div>
      </div>
      <div class="field is-grouped">
        <div class="control"><button class="button is-primary" type="submit">Save</button></div>
        <div class="control"><a class="button" href="/profile">Cancel</a></div>
      </div>
    </form>

    <hr>

    <h2 class="title is-5">Change Password</h2>
    <form class="box" method="post" action="/profile/password">
      <?php $this->csrfField(); ?>
      <div class="field">
        <label class="label" for="current_password">Current Password</label>
        <div class="control">
          <input class="input" id="current_password" type="password" name="current_password" required>
        </div>
      </div>
      <div class="field">
        <label class="label" for="new_password">New Password</label>
        <div class="control">
          <input class="input" id="new_password" type="password" name="new_password" required>
        </div>
      </div>
      <div class="field">
        <label class="label" for="new_password_confirm">Confirm New Password</label>
        <div class="control">
          <input class="input" id="new_password_confirm" type="password" name="new_password_confirm" required>
        </div>
      </div>
      <div class="field"><button class="button is-link" type="submit">Change Password</button></div>
    </form>
  </div>
</section>
<?php $this->end(); ?>
```

`src/Views/profile/change_password.php` (optional separate view if you split pages)
```php
<?php $this->layout('layouts/main'); $this->start('content'); ?>
<section class="section">
  <div class="container">
    <h1 class="title">Change Password</h1>
    <form class="box" method="post" action="/profile/password">
      <?php $this->csrfField(); ?>
      <div class="field"><label class="label" for="current_password">Current Password</label><div class="control"><input class="input" id="current_password" type="password" name="current_password" required></div></div>
      <div class="field"><label class="label" for="new_password">New Password</label><div class="control"><input class="input" id="new_password" type="password" name="new_password" required></div></div>
      <div class="field"><label class="label" for="new_password_confirm">Confirm New Password</label><div class="control"><input class="input" id="new_password_confirm" type="password" name="new_password_confirm" required></div></div>
      <div class="field"><button class="button is-link" type="submit">Change Password</button></div>
    </form>
  </div>
</section>
<?php $this->end(); ?>
```

—

## Step 10) Admin‑only Users Management (GET/POST)

All routes require `admin` role. Keep endpoints simple and POST for state changes.

Routes (`src/Routes/index.php`):
```php
use App\Controllers\Admin\UsersController; // new line

...

$router->get('/admin/users', UsersController::class, 'index'); // new line
$router->get('/admin/users/create', UsersController::class, 'create'); // new line
$router->post('/admin/users', UsersController::class, 'store'); // new line
$router->get('/admin/users/{id}/edit', UsersController::class, 'edit'); // new line
$router->post('/admin/users/{id}', UsersController::class, 'update'); // new line
$router->post('/admin/users/{id}/role', UsersController::class, 'updateRole'); // new line
$router->post('/admin/users/{id}/active', UsersController::class, 'updateActive'); // new line
```

Controller (`src/Controllers/Admin/UsersController.php`):
```php
<?php
// filepath: projects/06/src/Controllers/Admin/UsersController.php
namespace App\Controllers\Admin;

use App\Controller;
use App\Models\User;
use App\Support\Validator;

class UsersController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireRole('admin');
    }

    public function index(): void
    {
        $users = \App\Models\User::all(limit: 200, offset: 0, orderBy: '`id` DESC');
        $this->render('admin/users/index', ['title' => 'Users', 'users' => $users]);
    }

    public function create(): void
    {
        $this->render('admin/users/create', ['title' => 'Create User']);
    }

    public function store(): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect('/admin/users/create');
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
            $this->redirect('/admin/users/create');
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

    public function edit(int $id): void
    {
        $user = \App\Models\User::find($id);
        if (!$user) {
            $this->flash('User not found.', 'is-warning');
            $this->redirect('/admin/users');
        }
        $this->render('admin/users/edit', ['title' => 'Edit User', 'user' => $user]);
    }

    public function update(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect("/admin/users/{$id}/edit");
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
            $this->redirect("/admin/users/{$id}/edit");
        }

        \App\Models\User::update($id, compact('name','email','role','is_active'));
        $this->flash('User updated.', 'is-success');
        $this->redirect('/admin/users');
    }

    public function updateRole(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect('/admin/users');
        }
        $role = $_POST['role'] ?? 'user';
        $errs = \App\Support\Validator::validate(['role' => $role], ['role' => 'required|in:admin,editor,user']);
        if (!empty($errs)) {
            foreach (\App\Support\Validator::flattenErrors($errs) as $m) {
                $this->flash($m, 'is-warning');
            }
            $this->redirect('/admin/users');
        }
        \App\Models\User::update($id, ['role' => $role]);
        $this->flash('Role updated.', 'is-success');
        $this->redirect('/admin/users');
    }

    public function updateActive(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            return $this->redirect('/admin/users');
        }
        $isActive = isset($_POST['is_active']) ? 1 : 0; // checkbox presence
        \App\Models\User::update($id, ['is_active' => $isActive]);
        $this->flash('User status updated.', 'is-info');
        $this->redirect('/admin/users');
    }
}
```

Views:

`src/Views/admin/users/index.php`
```php
<?php $this->layout('layouts/main'); $this->start('content'); ?>
<section class="section">
  <div class="container">
    <div class="level">
      <h1 class="title level-left">Users</h1>
      <div class="level-right"><a class="button is-primary" href="/admin/users/create">New User</a></div>
    </div>
    <table class="table is-fullwidth is-striped">
      <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Active</th><th></th></tr></thead>
      <tbody>
        <?php foreach (($users ?? []) as $u): ?>
          <tr>
            <td><?= (int)$u['id'] ?></td>
            <td><?= $this->e($u['name']) ?></td>
            <td><?= $this->e($u['email']) ?></td>
            <td><?= $this->e($u['role']) ?></td>
            <td><?= (int)$u['is_active'] ? 'Yes' : 'No' ?></td>
            <td class="has-text-right">
              <a class="button is-small" href="/admin/users/<?= (int)$u['id'] ?>/edit">Edit</a>
              <form style="display:inline; margin-left:.5rem" method="post" action="/admin/users/<?= (int)$u['id'] ?>/active">
                <?php $this->csrfField(); ?>
                <label class="checkbox">
                  <input type="checkbox" name="is_active" value="1" <?= (int)$u['is_active'] ? 'checked' : '' ?> onchange="this.form.submit()">
                  Active
                </label>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<?php $this->end(); ?>
```

`src/Views/admin/users/create.php`
```php
<?php $this->layout('layouts/main'); $this->start('content'); ?>
<section class="section">
  <div class="container">
    <h1 class="title">Create User</h1>
    <form class="box" method="post" action="/admin/users">
      <?php $this->csrfField(); ?>
      <div class="field"><label class="label" for="name">Name</label><div class="control"><input class="input" id="name" type="text" name="name" required></div></div>
      <div class="field"><label class="label" for="email">Email</label><div class="control"><input class="input" id="email" type="email" name="email" required></div></div>
      <div class="field"><label class="label" for="role">Role</label><div class="control"><div class="select"><select id="role" name="role"><option value="user">user</option><option value="editor">editor</option><option value="admin">admin</option></select></div></div></div>
      <div class="field"><label class="label" for="password">Password</label><div class="control"><input class="input" id="password" type="password" name="password" required></div></div>
      <div class="field is-grouped"><div class="control"><button class="button is-primary" type="submit">Create</button></div><div class="control"><a class="button" href="/admin/users">Cancel</a></div></div>
    </form>
  </div>
</section>
<?php $this->end(); ?>
```

`src/Views/admin/users/edit.php`
```php
<?php $this->layout('layouts/main'); $this->start('content'); ?>
<section class="section">
  <div class="container">
    <h1 class="title">Edit User</h1>
    <form class="box" method="post" action="/admin/users/<?= (int)($user['id'] ?? 0) ?>">
      <?php $this->csrfField(); ?>
      <div class="field"><label class="label" for="name">Name</label><div class="control"><input class="input" id="name" type="text" name="name" value="<?= $this->e($user['name'] ?? '') ?>" required></div></div>
      <div class="field"><label class="label" for="email">Email</label><div class="control"><input class="input" id="email" type="email" name="email" value="<?= $this->e($user['email'] ?? '') ?>" required></div></div>
      <div class="field"><label class="label" for="role">Role</label><div class="control"><div class="select"><select id="role" name="role">
        <?php $roles = ['user','editor','admin']; foreach ($roles as $r): ?>
          <option value="<?= $r ?>" <?= (($user['role'] ?? 'user') === $r) ? 'selected' : '' ?>><?= $r ?></option>
        <?php endforeach; ?>
      </select></div></div></div>
      <div class="field"><label class="checkbox"><input type="checkbox" name="is_active" value="1" <?= ((int)($user['is_active'] ?? 1)) ? 'checked' : '' ?>> Active</label></div>
      <div class="field is-grouped"><div class="control"><button class="button is-primary" type="submit">Save</button></div><div class="control"><a class="button" href="/admin/users">Cancel</a></div></div>
    </form>

    <hr>

    <h2 class="title is-5">Update Role Only</h2>
    <form method="post" action="/admin/users/<?= (int)($user['id'] ?? 0) ?>/role">
      <?php $this->csrfField(); ?>
      <div class="field"><div class="select"><select name="role">
        <?php foreach ($roles as $r): ?>
          <option value="<?= $r ?>" <?= (($user['role'] ?? 'user') === $r) ? 'selected' : '' ?>><?= $r ?></option>
        <?php endforeach; ?>
      </select></div></div>
      <div class="field"><button class="button is-link" type="submit">Update Role</button></div>
    </form>
  </div>
</section>
<?php $this->end(); ?>
```

—

## Step 11) Routes (favor GET/POST)

Before wiring the new endpoints, update `src/Router.php` so it can dispatch routes with placeholders such as `/admin/users/{id}`. Store each route as a compiled regular expression with named parameters and hydrate them before invoking the controller:

`src/Router.php` (key updates)
```php
class Router
{
    protected $routes = [
        'GET' => [],
        'POST' => [],
    ];

    private function addRoute(string $route, string $controller, string $action, string $method): void
    {
        $paramNames = [];
        $pattern = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            function (array $m) use (&$paramNames): string {
                $paramNames[] = $m[1];
                return '(?P<' . $m[1] . '>[^/]+)';
            },
            $route
        );

        $this->routes[$method][] = [
            'pattern' => '#^' . $pattern . '$#',
            'controller' => $controller,
            'action' => $action,
            'params' => $paramNames,
        ];
    }

    public function get(string $route, string $controller, string $action): void
    {
        $this->addRoute($route, $controller, $action, 'GET');
    }

    public function post(string $route, string $controller, string $action): void
    {
        $this->addRoute($route, $controller, $action, 'POST');
    }

    public function dispatch(): void
    {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                $controller = new ($route['controller']);
                $params = array_map(fn(string $name) => $matches[$name] ?? null, $route['params']);
                $controller->{$route['action']}(...$params);
                return;
            }
        }

        throw new \Exception("No route for {$method} {$uri}");
    }
}
```

`src/Routes/index.php` complete:
```php
<?php

use App\Controllers\Admin\UsersController; // new line
use App\Controllers\ProfileController; // new line
use App\Controllers\AuthController; //new line
use App\Controllers\HomeController;
use App\Controllers\ContactController;
use App\Router;

$router = new Router();

$router->get('/', HomeController::class, 'index');
$router->get('/contact', ContactController::class, 'show');
$router->post('/contact', ContactController::class, 'submit');

$router->get('/register', AuthController::class, 'showRegister'); // new line
$router->post('/register', AuthController::class, 'register'); // new line

$router->get('/login', AuthController::class, 'showLogin'); // new line
$router->post('/login', AuthController::class, 'login'); // new line
$router->post('/logout', AuthController::class, 'logout'); // new line

$router->get('/profile', ProfileController::class, 'show'); // new line
$router->get('/profile/edit', ProfileController::class, 'edit'); // new line
$router->post('/profile', ProfileController::class, 'update'); // new line
$router->post('/profile/password', ProfileController::class, 'changePassword'); // new line

$router->get('/admin/users', UsersController::class, 'index'); // new line
$router->get('/admin/users/create', UsersController::class, 'create'); // new line
$router->post('/admin/users', UsersController::class, 'store'); // new line
$router->get('/admin/users/{id}/edit', UsersController::class, 'edit'); // new line
$router->post('/admin/users/{id}', UsersController::class, 'update'); // new line
$router->post('/admin/users/{id}/role', UsersController::class, 'updateRole'); // new line
$router->post('/admin/users/{id}/active', UsersController::class, 'updateActive');

$router->dispatch();

```

—

## Step 12) Role‑aware navigation and protected views

Update `src/Views/partials/nav.php` so it reflects auth state and roles:
- Guest: show “Login”, “Register”
- Authenticated: show “Profile”, “Logout” (logout must be a POST form with CSRF)
- Admins: show “Users” link to `/admin/users`

Example (excerpt):
```php
<?php $u = $_SESSION['user_id'] ?? null;
$role = $_SESSION['user_role'] ?? 'user'; ?>
<header class="container">
    <nav class="navbar is-fixed-top is-spaced has-shadow" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="/">
                <span class="icon-text">
                    <span class="icon"><i class="fas fa-code" aria-hidden="true"></i></span>
                    <span><?= $this->e($title ?? ($siteName ?? 'Site')) ?></span>
                </span>
            </a>
            <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarMenu">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>
        <div class="navbar-menu is-active">
            <div class="navbar-start">
                <?php if ($u): ?>
                    <a class="navbar-item" href="/profile">Profile</a>
                    <?php if ($role === 'admin'): ?>
                        <a class="navbar-item" href="/admin/users">Users</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="navbar-end">
                <?php if ($u): ?>
                    <div class="navbar-item">
                        <form method="post" action="/logout">
                            <?php $this->csrfField(); ?>
                            <button class="button is-light" type="submit">Logout</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="navbar-item">
                        <div class="buttons">
                            <a class="button is-light" href="/login">Login</a>
                            <a class="button is-primary" href="/register"><strong>Register</strong></a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>
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

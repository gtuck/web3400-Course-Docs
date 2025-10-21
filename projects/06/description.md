# Project 06 – User Accounts, Authentication, and Role-Based Access Control
Build on your Project 05 framework to add user registration, login/logout, and role management (admin, editor, user). Implement secure password storage, session-based authentication, profile management for users, and an admin interface to manage all user accounts.

---

## Overview
Starting from your completed Project 05 (templating, CSRF, validator, RESTful routing, custom exceptions), you will:

1. Create a `users` table and a `User` model
2. Implement secure registration (with validation, unique email, password hashing)
3. Implement login/logout with session management and CSRF protection
4. Add role-based authorization (admin, editor, user)
5. Add user profile management (view/edit profile, change password)
6. Build an admin-only users management area (list, create, edit, change role, deactivate/delete)
7. Integrate roles into navigation and protect routes/views accordingly

This is vanilla PHP — use only the tooling you built in earlier projects plus core PHP features (sessions, password_hash/password_verify).

---

## Learning Objectives
- Design a secure authentication flow using sessions
- Store passwords using modern hashing (`password_hash`, `password_verify`)
- Implement role-based authorization checks in controllers/routes
- Add custom validation rules (e.g., `unique`) on top of the P05 Validator
- Enforce CSRF protection on all state-changing requests (POST, PUT, PATCH, DELETE)
- Apply least-privilege design across controllers and views

---

## Prerequisites
- Start from your working Project 05 in `projects/05/` (templating, CSRF, Validator, RESTful routing, exceptions)
- Sessions already initialized in `public/index.php` (from P05 step 5a)
- Database connection via `Support/Database.php` (from P04)

---

## Target Structure

```
projects/06/
  composer.json
  public/
    index.php                # Starts session, loads routes
  src/
    Controller.php           # Base controller (flash, redirect, view, csrf)
    Router.php               # RESTful + method spoofing
    Support/
      Database.php
      Auth.php               # NEW – Auth service (session + helpers)
      Validator.php          # Extend with `unique` rule
    Models/
      BaseModel.php
      User.php               # NEW – users table model
    Controllers/
      AuthController.php     # NEW – register, login, logout
      ProfileController.php  # NEW – view/update own profile & password
      UsersController.php    # NEW – admin users management
    Routes/
      index.php              # Add /register, /login, /logout, /profile, /admin/users
    Views/
      auth/
        login.php
        register.php
      profile/
        show.php
        edit.php
      admin/users/
        index.php
        create.php
        edit.php
  scripts/
    create-admin.php        # NEW – CLI script to seed an admin
```

Use your P05 layout, partials, and flash messages; keep consistent XSS protection with `$this->e()` and include CSRF fields in forms.

---

## Step 1) Copy your Project 05 into `projects/06`

From the repository root:

```bash
cp -r projects/05 projects/06
```

Remove P05-specific references in routes and views that aren’t needed, then add the files described below.

Create the additional P06 folders and empty files:

```bash
cd projects/06 \
&& mkdir -p src/{Support,Models,Controllers,Routes,Views} \
&& mkdir -p src/Views/{auth,profile,admin/users} \
&& touch \
  src/Support/Auth.php \
  src/Models/User.php \
  src/Controllers/{AuthController.php,ProfileController.php,UsersController.php} \
  src/Routes/index.php \
  src/Views/auth/{login.php,register.php} \
  src/Views/profile/{show.php,edit.php,password.php} \
  src/Views/admin/users/{index.php,create.php,edit.php}
```

---

## Step 2) Create the `users` table

Create a `users` table with role and activity flags. Example SQL:

```sql
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','editor','user') NOT NULL DEFAULT 'user',
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (email)
);
```

Seed at least one admin user (manually insert a row with a known email; generate a `password_hash` using PHP or a quick script).

---

## Step 3) Add the `User` model

`src/Models/User.php` (complete example):

```php
<?php
// filepath: projects/06/src/Models/User.php
namespace App\Models;

use App\Support\Database;
use PDO;

final class User extends BaseModel
{
    protected static string $table = 'users';
    // Do NOT include 'role' or 'active' to prevent privilege escalation via mass assignment
    protected static array $fillable = ['name','email','password_hash'];

    public static function findByEmail(string $email): ?array
    {
        $sql = 'SELECT * FROM `users` WHERE `email` = :email LIMIT 1';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function countAdmins(): int
    {
        $stmt = Database::pdo()->query("SELECT COUNT(*) FROM `users` WHERE `role` = 'admin' AND `active` = 1");
        return (int)$stmt->fetchColumn();
    }

    // Admin-only create: allows role/active
    public static function adminCreate(array $data): int
    {
        $payload = [
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'password_hash' => $data['password_hash'] ?? null,
            'role' => $data['role'] ?? 'user',
            'active' => isset($data['active']) ? (int)$data['active'] : 1,
        ];
        $sql = 'INSERT INTO `users` (`name`,`email`,`password_hash`,`role`,`active`) VALUES (:name,:email,:password_hash,:role,:active)';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($payload);
        return (int)Database::pdo()->lastInsertId();
    }

    // Admin-only update: allows role/active
    public static function adminUpdate(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];
        foreach (['name','email','role','active'] as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "`{$col}` = :{$col}";
                $params[':'.$col] = ($col === 'active') ? (int)$data[$col] : $data[$col];
            }
        }
        if (!$fields) return false;
        $sql = 'UPDATE `users` SET ' . implode(', ', $fields) . ' WHERE `id` = :id';
        $stmt = Database::pdo()->prepare($sql);
        return $stmt->execute($params);
    }
}
```

Keep the style consistent with your existing models from P04/P05 (array-returning `all`, `firstWhere`, `create`, `update`, etc.).

---

## Step 4) Extend `Validator` with a `unique` rule

Add a `unique:table,column[,ignoreId]` rule that checks the database for duplicates. Examples:
- `unique:users,email` for registration
- `unique:users,email,{currentUserId}` for profile updates (ignore current user)

Add a `same:otherField` rule to compare fields (e.g., password confirmation).

Error message example: `The :attribute has already been taken.`

Update your P05 `Validator` to pass the full `$data` into rule evaluation and avoid relying on superglobals. Example implementation sketch:

```php
<?php
// filepath: projects/06/src/Support/Validator.php
namespace App\Support;

use App\Support\Database;

class Validator
{
    public static function validate(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $pipe) {
            $value = $data[$field] ?? null;
            foreach (explode('|', $pipe) as $rawRule) {
                [$rule, $param] = array_pad(explode(':', $rawRule, 2), 2, null);
                $msg = self::applyRule($data, $field, $value, $rule, $param);
                if ($msg) $errors[$field][] = $msg;
            }
        }
        return $errors;
    }

    private static function applyRule(array $data, string $field, mixed $value, string $rule, ?string $param): ?string
    {
        return match ($rule) {
            'required' => ($value === null || $value === '') ? ucfirst($field) . " is required." : null,
            'email' => ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) ? ucfirst($field) . " must be a valid email." : null,
            'max' => ($value && mb_strlen((string)$value) > (int)$param) ? ucfirst($field) . " must not exceed {$param} characters." : null,
            'min' => ($value && mb_strlen((string)$value) < (int)$param) ? ucfirst($field) . " must be at least {$param} characters." : null,
            'in' => ($value && !in_array((string)$value, explode(',', (string)$param), true)) ? ucfirst($field) . " must be one of: {$param}." : null,
            'unique' => self::validateUnique($field, $value, $param),
            'same' => self::validateSame($data, $field, $value, $param),
            default => null,
        };
    }

    private static function validateUnique(string $field, mixed $value, ?string $param): ?string
    {
        if ($value === null || $value === '' || !$param) return null;

        [$table, $column, $ignoreId] = array_pad(explode(',', $param), 3, null);

        // Security: validate/whitelist table & column names to avoid SQL injection via misconfigured rules
        $allowed = [
            'users' => ['email','name'],
        ];
        if (!isset($allowed[$table]) || !in_array($column, $allowed[$table], true)) {
            return 'Validation configuration error.';
        }

        $pdo = Database::pdo();
        if ($ignoreId) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = :val AND `id` != :id");
            $stmt->execute([':val' => $value, ':id' => $ignoreId]);
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = :val");
            $stmt->execute([':val' => $value]);
        }
        $count = (int)$stmt->fetchColumn();
        return $count > 0 ? "The {$field} has already been taken." : null;
    }

    private static function validateSame(array $data, string $field, mixed $value, ?string $param): ?string
    {
        if (!$param) return null;
        $compareValue = $data[$param] ?? null;
        return $value !== $compareValue ? ucfirst($field) . " must match {$param}." : null;
    }
}
```

**Security note:** When supporting `unique` across tables/columns, use a whitelist (as above) to prevent SQL injection through misconfigured rules. The implementation uses `Database::pdo()` for direct PDO access with prepared statements.

---

## Step 5) Implement the `Auth` service

`src/Support/Auth.php` should encapsulate session-based auth (complete example matching the sample app):

```php
<?php
// filepath: projects/06/src/Support/Auth.php
namespace App\Support;

use App\Models\User;
class Auth
{
    public static function login(array $user): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'] ?? 'user';
    }

    public static function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
    public static function user(): ?array
    {
        if (!isset($_SESSION['user_id'])) return null;
        return User::find($_SESSION['user_id']);
    }
    public static function check(): bool { return isset($_SESSION['user_id']); }
    public static function role(): ?string { return $_SESSION['user_role'] ?? null; }
    public static function authorize(array $roles): bool
    {
        $r = self::role();
        return $r !== null && in_array($r, $roles, true);
    }
}
```

Optional (recommended): add a `csrfField()` helper to your View class to output a hidden CSRF token field in forms.

```php
// In src/Support/View.php
public function csrfField(): void
{
    $token = $this->shared['csrfToken'] ?? '';
    echo '<input type="hidden" name="csrf_token" value="' . $this->e($token) . '">';
}
```

**Add authentication helper methods to Controller.php:**

In your `src/Controller.php`, add these helper methods to protect routes:

```php
/**
 * Require that a user is authenticated. If not, store intended URL and redirect to /login.
 */
protected function requireAuth(): void
{
    if (!Auth::check()) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        // Store intended URL for post-login redirect
        $_SESSION['intended'] = $_SERVER['REQUEST_URI'] ?? '/';
        $this->flash('Please log in to continue.', 'is-warning');
        $this->redirect('/login');
    }
}

/**
 * Require that the current user has one of the given roles.
 * @param string|array $roles
 */
protected function requireRole(string|array $roles): void
{
    $roles = is_array($roles) ? $roles : [$roles];
    $this->requireAuth();
    if (!Auth::authorize($roles)) {
        $this->flash('You are not authorized to access that page.', 'is-danger');
        $this->redirect('/');
    }
}
```

These methods should be called at the beginning of controller methods that require authentication or specific roles.

**Add CSRF helper to View.php (optional but recommended):**

To reduce duplication, add this helper method to your `src/Support/View.php`:

```php
/**
 * Output a CSRF token hidden input field
 *
 * Convenience helper to avoid repeating the same code in every form.
 * Automatically escapes the token value for safety.
 */
public function csrfField(): void
{
    $token = $this->shared['csrfToken'] ?? '';
    echo '<input type="hidden" name="csrf_token" value="' . $this->e($token) . '">';
}
```

Then in your views, use `<?php $this->csrfField(); ?>` instead of manually writing the hidden input.

---

## Step 6) Registration: routes, controller, and view

Routes (in `src/Routes/index.php`):
- `GET /register` → `AuthController@showRegister`
- `POST /register` → `AuthController@register`

Validation rules:
- `name`: `required|min:2|max:100`
- `email`: `required|email|max:255|unique:users,email`
- `password`: `required|min:8|max:255`
- `password_confirm`: must match `password` (add a `same:password` rule or validate manually)

**AuthController implementation** (`src/Controllers/AuthController.php`):

```php
<?php
// filepath: projects/06/src/Controllers/AuthController.php
namespace App\Controllers;

use App\Controller;
use App\Models\User;
use App\Support\Auth;
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
            $this->flash('Invalid security token.', 'is-danger');
            $this->redirect('/register');
        }

        $data = [
            'name' => trim((string)($_POST['name'] ?? '')),
            'email' => strtolower(trim((string)($_POST['email'] ?? ''))),
            'password' => (string)($_POST['password'] ?? ''),
            'password_confirm' => (string)($_POST['password_confirm'] ?? ''),
        ];

        $errors = Validator::validate($data, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:8|max:255',
            'password_confirm' => 'required|same:password',
        ]);

        if (!empty($errors)) {
            foreach (Validator::flattenErrors($errors) as $e) {
                $this->flash($e, 'is-danger');
            }
            $this->redirect('/register');
        }

        $id = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
        ]);

        $user = User::find($id);
        Auth::login($user);
        $this->flash('Account created. Welcome!', 'is-success');
        $this->redirect('/profile');
    }
}
```

**Register view** (`src/Views/auth/register.php`):

```php
<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>

<section class="section">
  <div class="container">
    <h1 class="title">Register</h1>
    <form method="post" action="/register" class="box" novalidate>
      <?php $this->csrfField(); ?>
      <div class="field">
        <label class="label" for="name">Name</label>
        <div class="control">
          <input class="input" type="text" id="name" name="name" required>
        </div>
      </div>
      <div class="field">
        <label class="label" for="email">Email</label>
        <div class="control">
          <input class="input" type="email" id="email" name="email" required>
        </div>
      </div>
      <div class="field">
        <label class="label" for="password">Password</label>
        <div class="control">
          <input class="input" type="password" id="password" name="password" required>
        </div>
      </div>
      <div class="field">
        <label class="label" for="password_confirm">Confirm Password</label>
        <div class="control">
          <input class="input" type="password" id="password_confirm" name="password_confirm" required>
        </div>
      </div>
      <div class="field">
        <div class="control">
          <button type="submit" class="button is-primary">Create Account</button>
          <a href="/login" class="button is-text">Already have an account?</a>
        </div>
      </div>
    </form>
  </div>
</section>

<?php $this->end(); ?>
```

---

## Step 7) Login/Logout: routes, controller, and view

Routes:
- `GET /login` → `AuthController@showLogin`
- `POST /login` → `AuthController@login`
- `POST /logout` → `AuthController@logout`

`src/Routes/index.php` additions (complete example):

```php
<?php
// filepath: projects/06/src/Routes/index.php
use App\Controllers\HomeController;
use App\Controllers\ContactController;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\UsersController;
use App\Router;

$router = new Router();

$router->get('/', HomeController::class, 'index');
$router->get('/contact', ContactController::class, 'show');
$router->post('/contact', ContactController::class, 'submit');

// Auth
$router->get('/login', AuthController::class, 'showLogin');
$router->post('/login', AuthController::class, 'login');
$router->get('/register', AuthController::class, 'showRegister');
$router->post('/register', AuthController::class, 'register');
$router->post('/logout', AuthController::class, 'logout');

// Profile
$router->get('/profile', ProfileController::class, 'show');
$router->get('/profile/edit', ProfileController::class, 'edit');
$router->post('/profile', ProfileController::class, 'update');
$router->post('/profile/password', ProfileController::class, 'updatePassword');

// Admin: Users
$router->get('/admin/users', UsersController::class, 'index');
$router->get('/admin/users/create', UsersController::class, 'create');
$router->post('/admin/users', UsersController::class, 'store');
$router->get('/admin/users/edit', UsersController::class, 'edit'); // expects ?id=123
$router->post('/admin/users/update', UsersController::class, 'update');
$router->post('/admin/users/delete', UsersController::class, 'destroy');

$router->dispatch();
```

**Add login/logout methods to AuthController:**

```php
public function showLogin(): void
{
    $this->render('auth/login', ['title' => 'Login']);
}

public function login(): void
{
    if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
        $this->flash('Invalid security token.', 'is-danger');
        $this->redirect('/login');
    }

    $data = [
        'email' => trim((string)($_POST['email'] ?? '')),
        'password' => (string)($_POST['password'] ?? ''),
    ];

    $errors = Validator::validate($data, [
        'email' => 'required|email|max:255',
        'password' => 'required|min:8|max:255',
    ]);

    if (!empty($errors)) {
        foreach (Validator::flattenErrors($errors) as $e) {
            $this->flash($e, 'is-danger');
        }
        $this->redirect('/login');
    }

    $user = User::findByEmail(strtolower($data['email']));
    // Use generic error message - don't reveal if email exists
    if (!$user || (int)($user['active'] ?? 1) !== 1 || !password_verify($data['password'], $user['password_hash'])) {
        $this->flash('Invalid credentials.', 'is-danger');
        $this->redirect('/login');
    }

    Auth::login($user);
    // Redirect to intended URL if stored, otherwise go home
    $dest = $_SESSION['intended'] ?? '/';
    unset($_SESSION['intended']);
    $this->flash('Welcome back!', 'is-success');
    $this->redirect($dest);
}

public function logout(): void
{
    if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
        $this->flash('Invalid security token.', 'is-danger');
        $this->redirect('/');
    }
    Auth::logout();
    $this->flash('You have been logged out.', 'is-info');
    $this->redirect('/');
}
```

**Login view** (`src/Views/auth/login.php`):

```php
<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>

<section class="section">
  <div class="container">
    <h1 class="title">Login</h1>
    <form method="post" action="/login" class="box" novalidate>
      <?php $this->csrfField(); ?>
      <div class="field">
        <label class="label" for="email">Email</label>
        <div class="control">
          <input class="input" type="email" id="email" name="email" required>
        </div>
      </div>
      <div class="field">
        <label class="label" for="password">Password</label>
        <div class="control">
          <input class="input" type="password" id="password" name="password" required>
        </div>
      </div>
      <div class="field">
        <div class="control">
          <button type="submit" class="button is-primary">Login</button>
          <a href="/register" class="button is-text">Create an account</a>
        </div>
      </div>
    </form>
  </div>
</section>

<?php $this->end(); ?>
```

---

## Step 8) Role-based authorization

Define roles: `admin`, `editor`, `user`.

Common patterns:
- Protect routes with guards before executing controller actions
  - Example: `requireRole(['admin'])` for `/admin/users/*`
  - Example: `requireRole(['admin','editor'])` for editor-only features
- Hide or show navigation/menu items based on role
- On the server, always re-check roles (don’t rely on hidden UI)

You can implement route-level guards in the router (middleware-like callbacks) or in controllers using helper methods in `Controller.php`.

---

## Step 9) Profile management (for the current user)

Routes:
- `GET /profile` → `ProfileController@show` (requires auth)
- `GET /profile/edit` → `ProfileController@edit`
- `POST /profile` → `ProfileController@update`
- `POST /profile/password` → `ProfileController@updatePassword`

Update validations:
- `name`: `required|min:2|max:100`
- `email`: `required|email|max:255|unique:users,email,{currentUserId}`

**ProfileController implementation** (`src/Controllers/ProfileController.php`):

```php
<?php
// filepath: projects/06/src/Controllers/ProfileController.php
namespace App\Controllers;

use App\Controller;
use App\Models\User;
use App\Support\Auth;
use App\Support\Validator;

class ProfileController extends Controller
{
    public function show(): void
    {
        $this->requireAuth();
        $this->render('profile/show', [
            'title' => 'Your Profile',
            'user' => Auth::user(),
        ]);
    }

    public function edit(): void
    {
        $this->requireAuth();
        $this->render('profile/edit', [
            'title' => 'Edit Profile',
            'user' => Auth::user(),
        ]);
    }

    public function update(): void
    {
        $this->requireAuth();
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid security token.', 'is-danger');
            $this->redirect('/profile/edit');
        }

        $user = Auth::user();
        $data = [
            'name' => trim((string)($_POST['name'] ?? '')),
            'email' => strtolower(trim((string)($_POST['email'] ?? ''))),
        ];

        $errors = Validator::validate($data, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . $user['id'],
        ]);

        if (!empty($errors)) {
            foreach (Validator::flattenErrors($errors) as $e) {
                $this->flash($e, 'is-danger');
            }
            $this->redirect('/profile/edit');
        }

        // Mass assignment protection: only update name and email
        User::update((int)$user['id'], $data);
        $this->flash('Profile updated.', 'is-success');
        $this->redirect('/profile');
    }

    public function updatePassword(): void
    {
        $this->requireAuth();
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid security token.', 'is-danger');
            $this->redirect('/profile');
        }

        $user = Auth::user();
        $data = [
            'current_password' => (string)($_POST['current_password'] ?? ''),
            'password' => (string)($_POST['password'] ?? ''),
            'password_confirm' => (string)($_POST['password_confirm'] ?? ''),
        ];

        $errors = Validator::validate($data, [
            'current_password' => 'required',
            'password' => 'required|min:8|max:255',
            'password_confirm' => 'required|same:password',
        ]);

        if (!empty($errors)) {
            foreach (Validator::flattenErrors($errors) as $e) {
                $this->flash($e, 'is-danger');
            }
            $this->redirect('/profile');
        }

        // Verify current password
        if (!password_verify($data['current_password'], $user['password_hash'])) {
            $this->flash('Current password is incorrect.', 'is-danger');
            $this->redirect('/profile');
        }

        User::update((int)$user['id'], [
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
        $this->flash('Password updated.', 'is-success');
        $this->redirect('/profile');
    }
}
```

**Profile show view** (`src/Views/profile/show.php`):

```php
<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>

<section class="section">
  <div class="container">
    <h1 class="title">Your Profile</h1>
    <div class="box">
      <p><strong>Name:</strong> <?= $this->e($user['name'] ?? '') ?></p>
      <p><strong>Email:</strong> <?= $this->e($user['email'] ?? '') ?></p>
      <p><strong>Role:</strong> <?= $this->e($user['role'] ?? 'user') ?></p>
      <div class="buttons mt-4">
        <a class="button is-link" href="/profile/edit">Edit Profile</a>
      </div>
    </div>

    <h2 class="subtitle">Change Password</h2>
    <form method="post" action="/profile/password" class="box" novalidate>
      <?php $this->csrfField(); ?>
      <div class="field">
        <label class="label" for="current_password">Current Password</label>
        <div class="control">
          <input class="input" type="password" id="current_password" name="current_password" required>
        </div>
      </div>
      <div class="field">
        <label class="label" for="password">New Password</label>
        <div class="control">
          <input class="input" type="password" id="password" name="password" required>
        </div>
      </div>
      <div class="field">
        <label class="label" for="password_confirm">Confirm New Password</label>
        <div class="control">
          <input class="input" type="password" id="password_confirm" name="password_confirm" required>
        </div>
      </div>
      <div class="field">
        <button class="button is-primary" type="submit">Update Password</button>
      </div>
    </form>
  </div>
</section>

<?php $this->end(); ?>
```

**Profile edit view** (`src/Views/profile/edit.php`):

```php
<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>

<section class="section">
  <div class="container">
    <h1 class="title">Edit Profile</h1>
    <form method="post" action="/profile" class="box" novalidate>
      <?php $this->csrfField(); ?>
      <div class="field">
        <label class="label" for="name">Name</label>
        <div class="control">
          <input class="input" type="text" id="name" name="name"
                 value="<?= $this->e($user['name'] ?? '') ?>" required>
        </div>
      </div>
      <div class="field">
        <label class="label" for="email">Email</label>
        <div class="control">
          <input class="input" type="email" id="email" name="email"
                 value="<?= $this->e($user['email'] ?? '') ?>" required>
        </div>
      </div>
      <div class="field">
        <button class="button is-primary" type="submit">Save</button>
        <a class="button" href="/profile">Cancel</a>
      </div>
    </form>
  </div>
</section>

<?php $this->end(); ?>
```

**Mass assignment safety:** The ProfileController only updates `name` and `email` fields, preventing users from changing their `role` or `active` status.

---

## Step 10) Admin: users management

Routes (admin only):
- `GET /admin/users` → list users
- `GET /admin/users/create` → show form
- `POST /admin/users` → create user
- `GET /admin/users/edit?id=123` → edit user
- `POST /admin/users/update` → update user
- `POST /admin/users/delete` → deactivate user

Behaviors:
- Admin can set/change `role` (`admin`, `editor`, `user`)
- Admin can deactivate a user (`active = 0`) instead of hard delete (recommended)
- Admin cannot deactivate themselves if they are the last admin (simple rule: prevent when count(admins)=1)

Validations (admin create/edit):
- `name`: `required|min:2|max:100`
- `email`: `required|email|max:255|unique:users,email,{ignoreId}`
- `role`: `required|in:admin,editor,user`

**UsersController implementation** (`src/Controllers/UsersController.php`):

```php
<?php
// filepath: projects/06/src/Controllers/UsersController.php
namespace App\Controllers;

use App\Controller;
use App\Models\User;
use App\Support\Validator;

class UsersController extends Controller
{
    public function index(): void
    {
        $this->requireRole('admin');
        $users = User::all(orderBy: 'created_at DESC');
        $this->render('admin/users/index', ['title' => 'Users', 'users' => $users]);
    }

    public function create(): void
    {
        $this->requireRole('admin');
        $this->render('admin/users/create', ['title' => 'Create User']);
    }

    public function store(): void
    {
        $this->requireRole('admin');
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid security token.', 'is-danger');
            $this->redirect('/admin/users/create');
        }
        $data = [
            'name' => trim((string)($_POST['name'] ?? '')),
            'email' => strtolower(trim((string)($_POST['email'] ?? ''))),
            'role' => (string)($_POST['role'] ?? 'user'),
            'password' => (string)($_POST['password'] ?? ''),
        ];
        $errors = Validator::validate($data, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|in:admin,editor,user',
            'password' => 'required|min:8|max:255',
        ]);
        if (!empty($errors)) {
            foreach (Validator::flattenErrors($errors) as $e) $this->flash($e, 'is-danger');
            $this->redirect('/admin/users/create');
        }
        User::adminCreate([
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'active' => 1,
        ]);
        $this->flash('User created.', 'is-success');
        $this->redirect('/admin/users');
    }

    public function edit(): void
    {
        $this->requireRole('admin');
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0 || !($user = User::find($id))) {
            $this->flash('User not found.', 'is-danger');
            $this->redirect('/admin/users');
        }
        $this->render('admin/users/edit', ['title' => 'Edit User', 'user' => $user]);
    }

    public function update(): void
    {
        $this->requireRole('admin');
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid security token.', 'is-danger');
            $this->redirect('/admin/users');
        }
        $id = (int)($_POST['id'] ?? 0);
        $user = $id ? User::find($id) : null;
        if (!$user) {
            $this->flash('User not found.', 'is-danger');
            $this->redirect('/admin/users');
        }
        $data = [
            'name' => trim((string)($_POST['name'] ?? '')),
            'email' => strtolower(trim((string)($_POST['email'] ?? ''))),
            'role' => (string)($_POST['role'] ?? $user['role']),
            'active' => isset($_POST['active']) ? 1 : 0,
        ];
        $errors = Validator::validate($data, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,editor,user',
        ]);
        if (!empty($errors)) {
            foreach (Validator::flattenErrors($errors) as $e) $this->flash($e, 'is-danger');
            $this->redirect('/admin/users/edit?id=' . $id);
        }
        // Prevent removing the last admin
        $demotingAdmin = ($user['role'] === 'admin') && ($data['role'] !== 'admin' || $data['active'] !== 1);
        if ($demotingAdmin && User::countAdmins() <= 1) {
            $this->flash('Cannot remove the last remaining admin.', 'is-danger');
            $this->redirect('/admin/users/edit?id=' . $id);
        }
        User::adminUpdate($id, $data);
        $this->flash('User updated.', 'is-success');
        $this->redirect('/admin/users');
    }

    public function destroy(): void
    {
        $this->requireRole('admin');
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid security token.', 'is-danger');
            $this->redirect('/admin/users');
        }
        $id = (int)($_POST['id'] ?? 0);
        $user = $id ? User::find($id) : null;
        if (!$user) {
            $this->flash('User not found.', 'is-danger');
            $this->redirect('/admin/users');
        }
        if ($user['role'] === 'admin' && User::countAdmins() <= 1) {
            $this->flash('Cannot remove the last remaining admin.', 'is-danger');
            $this->redirect('/admin/users');
        }
        User::adminUpdate($id, ['active' => 0]);
        $this->flash('User deactivated.', 'is-success');
        $this->redirect('/admin/users');
    }
}
```

Views:
- `admin/users/index.php`:

```php
<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>
<section class="section"><div class="container">
  <h1 class="title">Users</h1>
  <div class="mb-4"><a class="button is-primary" href="/admin/users/create">New User</a></div>
  <div class="table-container">
    <table class="table is-fullwidth is-striped">
      <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Active</th><th></th></tr></thead>
      <tbody>
        <?php foreach (($users ?? []) as $u): ?>
          <tr>
            <td><?= (int)$u['id'] ?></td>
            <td><?= $this->e($u['name']) ?></td>
            <td><?= $this->e($u['email']) ?></td>
            <td><?= $this->e($u['role']) ?></td>
            <td><?= ((int)$u['active'] === 1) ? 'Yes' : 'No' ?></td>
            <td>
              <a class="button is-small" href="/admin/users/edit?id=<?= (int)$u['id'] ?>">Edit</a>
              <?php if ((int)$u['active'] === 1): ?>
              <form method="post" action="/admin/users/delete" style="display:inline">
                <?php $this->csrfField(); ?>
                <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                <button type="submit" class="button is-small is-danger">Deactivate</button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div></section>
<?php $this->end(); ?>
```

- `admin/users/create.php`:

```php
<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>
<section class="section"><div class="container">
  <h1 class="title">Create User</h1>
  <form class="box" method="post" action="/admin/users" novalidate>
    <?php $this->csrfField(); ?>
    <div class="field"><label class="label" for="name">Name</label>
      <div class="control"><input class="input" type="text" id="name" name="name" required></div>
    </div>
    <div class="field"><label class="label" for="email">Email</label>
      <div class="control"><input class="input" type="email" id="email" name="email" required></div>
    </div>
    <div class="field"><label class="label" for="role">Role</label>
      <div class="control"><div class="select">
        <select id="role" name="role" required>
          <option value="user">user</option>
          <option value="editor">editor</option>
          <option value="admin">admin</option>
        </select>
      </div></div>
    </div>
    <div class="field"><label class="label" for="password">Password</label>
      <div class="control"><input class="input" type="password" id="password" name="password" required></div>
    </div>
    <div class="field">
      <button type="submit" class="button is-primary">Create</button>
      <a href="/admin/users" class="button">Cancel</a>
    </div>
  </form>
</div></section>
<?php $this->end(); ?>
```

- `admin/users/edit.php`:

```php
<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>
<section class="section"><div class="container">
  <h1 class="title">Edit User</h1>
  <form class="box" method="post" action="/admin/users/update" novalidate>
    <?php $this->csrfField(); ?>
    <input type="hidden" name="id" value="<?= (int)($user['id'] ?? 0) ?>">
    <div class="field"><label class="label" for="name">Name</label>
      <div class="control"><input class="input" type="text" id="name" name="name" value="<?= $this->e($user['name'] ?? '') ?>" required></div>
    </div>
    <div class="field"><label class="label" for="email">Email</label>
      <div class="control"><input class="input" type="email" id="email" name="email" value="<?= $this->e($user['email'] ?? '') ?>" required></div>
    </div>
    <div class="field"><label class="label" for="role">Role</label>
      <div class="control"><div class="select">
        <select id="role" name="role" required>
          <?php $role = $user['role'] ?? 'user'; ?>
          <option value="user" <?= $role==='user'?'selected':'' ?>>user</option>
          <option value="editor" <?= $role==='editor'?'selected':'' ?>>editor</option>
          <option value="admin" <?= $role==='admin'?'selected':'' ?>>admin</option>
        </select>
      </div></div>
    </div>
    <div class="field"><label class="checkbox">
      <input type="checkbox" name="active" value="1" <?= ((int)($user['active'] ?? 0) === 1) ? 'checked' : '' ?>> Active
    </label></div>
    <div class="field">
      <button type="submit" class="button is-primary">Save</button>
      <a href="/admin/users" class="button">Cancel</a>
    </div>
  </form>
</div></section>
<?php $this->end(); ?>
```

---

## Step 11) Navigation and UX

- Update `partials/nav.php` to conditionally show links based on `Auth::check()` and `Auth::role()`
  - Show `Register/Login` when logged out
  - Show `Profile`, `Logout` when logged in
  - Show `Admin` menu when role is `admin`
- Ensure all forms include a CSRF token (reuse P05 helper)
- Use flash messages for success and error feedback

Example `src/Views/partials/nav.php` update:

```php
<header class="container">
  <nav class="navbar is-fixed-top is-spaced has-shadow" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
      <a class="navbar-item" href="/">
        <span class="icon-text">
          <span class="icon"><i class="fas fa-2x fa-code" aria-hidden="true"></i></span>
          <span>&nbsp;&nbsp;<?= $this->e($title ?? ($siteName ?? 'Site')) ?></span>
        </span>
      </a>
    </div>
    <div id="navbarMenu" class="navbar-menu">
      <div class="navbar-start"></div>
      <div class="navbar-end">
        <a class="navbar-item" href="/contact">Contact</a>
        <?php if (!($isAuthenticated ?? false)): ?>
          <a class="navbar-item" href="/register">Register</a>
          <a class="navbar-item" href="/login">Login</a>
        <?php else: ?>
          <?php if (($userRole ?? '') === 'admin'): ?>
            <a class="navbar-item" href="/admin/users">Admin</a>
          <?php endif; ?>
          <a class="navbar-item" href="/profile">Profile</a>
          <form class="navbar-item" method="post" action="/logout" style="display:inline;">
            <?php $this->csrfField(); ?>
            <button class="button is-light" type="submit">Logout</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </nav>
</header>
```

---

## Step 12) Security checklist

- Passwords stored with `password_hash(PASSWORD_DEFAULT)`; verified with `password_verify`
- All state-changing requests (POST/PUT/PATCH/DELETE) are CSRF-protected (e.g., register, login, logout, update, delete)
- Input validation on all forms; server-side enforcement (don’t trust client)
- Email is unique in the database; unique check enforced server-side
- Session fixation prevented via `session_regenerate_id(true)` on login/logout
- Avoid revealing whether an email exists during login errors
- Role checks enforced in controllers/routes, not just hidden in views
- Validator `unique` rule uses a whitelist for table/column names
- User model does not allow mass-assigning `role` or `active` outside admin flows

---

## Run and Test

1. Create the `users` table and seed one admin user
2. Register a new account; verify you can log in/out
3. Edit your profile and change your password
4. Log in as admin; visit `/admin/users` and manage accounts
5. Verify that an editor cannot access admin routes
6. Check that CSRF protection blocks forged POST/DELETE requests
7. Confirm navigation updates based on authentication state/role

---

## Tips and Gotchas

- Use your P05 `Validator` consistently; extend it minimally for `unique` and `same` rules
- Centralize auth logic in `Support/Auth.php` so controllers stay thin
- Prefer soft deletes (or `active` flag) for safety; always filter `active=1` for login
- Normalize emails (e.g., `trim`, `strtolower`) before validation and storage
- Use `$this->e()` in views to avoid XSS; never echo raw user input
- Consider adding simple rate limiting for login (e.g., sleep on failure)

---

## Grading Checklist

Core Authentication
- [ ] Users can register with validated inputs and unique email
- [ ] Passwords are hashed on create and update; never stored in plain text
- [ ] Users can log in and log out; sessions managed securely

Profiles
- [ ] Authenticated users can view and update their profile (name, email)
- [ ] Authenticated users can change their password (with current password confirmation)

Roles & Authorization
- [ ] Roles implemented: `admin`, `editor`, `user`
- [ ] Protected routes: admin area requires `admin`; editor-only features restricted
- [ ] Navigation adapts to authentication state and role

Admin User Management
- [ ] Admin can list, create, edit users; set roles; deactivate/delete
- [ ] Admin cannot remove the last remaining admin

Security & UX
- [ ] All state-changing requests are CSRF-protected
- [ ] Validator extended with `unique` (and `same` or equivalent)
- [ ] Flash messages and validation errors are shown in views

Code Quality
- [ ] Changes integrate cleanly with P05 structure (Controllers/Models/Views/Support)
- [ ] Minimal duplication; helpers used where appropriate

---

## Submission

- Commit your work under `projects/06/`
- Ensure the `users` table exists and at least one admin account is configured (document credentials for grading in a `README` inside `projects/06/` or seed script)
- Provide brief instructions to run the app (DB setup, local URL)

---

## Admin Seeder (optional)

Add a simple CLI script to create an initial admin user:

```php
<?php
// filepath: projects/06/scripts/create-admin.php
#!/usr/bin/env php
declare(strict_types=1);
$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable($root); $dotenv->safeLoad();

use App\Support\Database;

[$name, $email, $password, $role] = $argv + [null, null, null, 'admin'];
if (!$name || !$email || !$password) {
    fwrite(STDERR, "Usage: php scripts/create-admin.php \"Admin Name\" admin@example.com \"PlaintextPassword\" [role]\n");
    exit(1);
}
if (!in_array($role, ['admin','editor','user'], true)) { fwrite(STDERR, "Invalid role\n"); exit(1); }
$pdo = Database::pdo();
$pdo->exec("CREATE TABLE IF NOT EXISTS users (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, email VARCHAR(255) NOT NULL UNIQUE, password_hash VARCHAR(255) NOT NULL, role ENUM('admin','editor','user') NOT NULL DEFAULT 'user', active TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX (email))");
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1'); $stmt->execute([':email' => strtolower($email)]);
if ($stmt->fetchColumn()) { fwrite(STDERR, "User exists\n"); exit(1); }
$stmt = $pdo->prepare('INSERT INTO users (name,email,password_hash,role,active) VALUES (:n,:e,:h,:r,1)');
$stmt->execute([':n' => $name, ':e' => strtolower($email), ':h' => password_hash($password, PASSWORD_DEFAULT), ':r' => $role]);
fwrite(STDOUT, "Created admin user #" . $pdo->lastInsertId() . "\n");
```

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
        password.php
      admin/users/
        index.php
        create.php
        edit.php
  scripts/
    # optional: user seeder or utilities
```

Use your P05 layout, partials, and flash messages; keep consistent XSS protection with `$this->e()` and include CSRF fields in forms.

---

## Step 1) Copy your Project 05 into `projects/06`

From the repository root:

```bash
cp -r projects/05 projects/06
```

Remove P05-specific references in routes and views that aren’t needed, then add the files described below.

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

`src/Models/User.php` (example outline):

```php
<?php
// filepath: projects/06/src/Models/User.php
namespace App\Models;

class User extends BaseModel
{
    protected static $table = 'users';
    // Note: Do NOT include 'role' or 'active' here to prevent mass assignment by non-admin flows
    protected static $fillable = ['name','email','password_hash'];

    public static function findByEmail(string $email)
    {
        return static::firstWhere('email = ?', [$email]);
    }

    // Admin-only updates should set 'role' and 'active' explicitly (not via mass assignment)
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

        $db = Database::getInstance();
        if ($ignoreId) {
            $sql = "SELECT COUNT(*) AS count FROM {$table} WHERE {$column} = ? AND id != ?";
            $result = $db->query($sql, [$value, $ignoreId]);
        } else {
            $sql = "SELECT COUNT(*) AS count FROM {$table} WHERE {$column} = ?";
            $result = $db->query($sql, [$value]);
        }
        $count = (int)($result[0]['count'] ?? 0);
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

Security note: when supporting `unique` across tables/columns, use a whitelist (as above) or map rule tokens to known table/column pairs to prevent SQL injection through misconfigured rules.

---

## Step 5) Implement the `Auth` service

`src/Support/Auth.php` should encapsulate session-based auth:

```php
<?php
// filepath: projects/06/src/Support/Auth.php
namespace App\Support;

use App\Models\User;

/**
 * Auth service - handles session-based authentication and authorization
 *
 * Provides static methods to log users in/out, check authentication state,
 * retrieve the current user, and verify role-based permissions.
 */
class Auth
{
    /**
     * Log in a user by storing their ID and role in the session
     * Regenerates session ID to prevent session fixation attacks
     */
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
    }

    /**
     * Log out the current user by clearing session data
     * Properly destroys session cookie and regenerates ID
     */
    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        session_regenerate_id(true);
    }

    /**
     * Get the currently authenticated user's data
     * Returns null if no user is logged in
     */
    public static function user(): ?array
    {
        if (!isset($_SESSION['user_id'])) return null;
        return User::find($_SESSION['user_id']);
    }

    /**
     * Check if a user is currently authenticated
     */
    public static function check(): bool { return isset($_SESSION['user_id']); }

    /**
     * Get the current user's role
     */
    public static function role(): ?string { return $_SESSION['user_role'] ?? null; }

    /**
     * Check if the current user has one of the specified roles
     *
     * @param array $roles Array of role names to check against
     * @return bool True if user has any of the specified roles
     */
    public static function authorize(array $roles): bool
    {
        $r = self::role();
        return $r !== null && in_array($r, $roles, true);
    }
}
```

You may also add convenience methods in `Controller.php`: `requireAuth()` and `requireRole($roles)` that redirect with a flash message when unauthorized.

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

Controller outline (`src/Controllers/AuthController.php`):
1. Validate inputs (use P05 `Validator` + new rules)
2. Hash password: `$hash = password_hash($password, PASSWORD_DEFAULT);`
3. Create user with default role `user`
4. Log the user in via `Auth::login($user)` and redirect to `/profile`

View (`src/Views/auth/register.php`):
- Use your P05 layout + partials, include `csrf_field` hidden input, show validation errors via flash or inline

---

## Step 7) Login/Logout: routes, controller, and view

Routes:
- `GET /login` → `AuthController@showLogin`
- `POST /login` → `AuthController@login`
- `POST /logout` → `AuthController@logout` (use method spoofing if you prefer `DELETE /logout`)

Login flow:
1. Validate email/password presence
2. Find user by email; if not found or `!active`, fail with generic error
3. Verify password: `password_verify($input, $user['password_hash'])`
4. `Auth::login($user)` + redirect to intended URL or `/`

Intended redirect tip:
- Before forcing login for a protected route, store the original URL in session, e.g., `$_SESSION['intended'] = $_SERVER['REQUEST_URI'];`
- After successful login, if `$_SESSION['intended']` is set, redirect there and then unset it; otherwise redirect to `/`.

Logout flow:
- CSRF-protected POST; call `Auth::logout()`; flash a message and redirect home

View (`src/Views/auth/login.php`):
- Email + password fields, CSRF token, show flash messages

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

Password change flow:
- Require current password, new password, confirm new password
- Verify current password with `password_verify`
- Validate new password strength (`min:8` at minimum)
- Update `password_hash` with `password_hash(PASSWORD_DEFAULT)`

Views:
- `profile/show.php`: basic account overview (name, email, role)
- `profile/edit.php`: form to update name/email
- `profile/password.php`: form to update password

Mass assignment safety:
- Do not allow profile updates to set `role` or `active`. Only accept `name` and `email` from profile forms, and pass only those fields to the model’s `update()`.

---

## Step 10) Admin: users management

Routes (admin only):
- `GET /admin/users` → list users
- `GET /admin/users/create` → show form
- `POST /admin/users` → create user
- `GET /admin/users/{id}/edit` → edit user
- `PATCH /admin/users/{id}` → update user (use method spoofing)
- `DELETE /admin/users/{id}` → deactivate/delete user

Behaviors:
- Admin can set/change `role` (`admin`, `editor`, `user`)
- Admin can deactivate a user (`active = 0`) instead of hard delete (recommended)
- Admin cannot deactivate themselves if they are the last admin (simple rule: prevent when count(admins)=1)

Validations (admin create/edit):
- `name`: `required|min:2|max:100`
- `email`: `required|email|max:255|unique:users,email,{ignoreId}`
- `role`: `required|in:admin,editor,user`

Views:
- `admin/users/index.php`: table with name, email, role, active, actions
- `admin/users/create.php`: create form (optional password auto-generation)
- `admin/users/edit.php`: edit form (role dropdown, active flag)

---

## Step 11) Navigation and UX

- Update `partials/nav.php` to conditionally show links based on `Auth::check()` and `Auth::role()`
  - Show `Register/Login` when logged out
  - Show `Profile`, `Logout` when logged in
  - Show `Admin` menu when role is `admin`
- Ensure all forms include a CSRF token (reuse P05 helper)
- Use flash messages for success and error feedback

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

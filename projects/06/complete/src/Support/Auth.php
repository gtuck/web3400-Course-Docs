<?php
/**
 * Auth Service
 *
 * PURPOSE:
 * Handles session-based authentication and authorization for the application.
 * Provides static methods to log users in/out, check authentication state,
 * retrieve the current user, and verify role-based permissions.
 *
 * USAGE EXAMPLE:
 * ```php
 * // Log in a user
 * $user = User::findByEmail('user@example.com');
 * Auth::login($user);
 *
 * // Check if user is authenticated
 * if (Auth::check()) {
 *     $currentUser = Auth::user();
 * }
 *
 * // Check if user has admin role
 * if (Auth::authorize(['admin'])) {
 *     // Admin-only code
 * }
 *
 * // Log out
 * Auth::logout();
 * ```
 *
 * SECURITY FEATURES:
 * - Session regeneration on login to prevent session fixation attacks
 * - Proper session destruction on logout (clears data and cookies)
 * - Role-based authorization checks
 */

namespace App\Support;

use App\Models\User;

class Auth
{
    /**
     * Log in a user by storing their ID and role in the session
     *
     * Regenerates the session ID to prevent session fixation attacks.
     * This should be called after verifying credentials (email + password).
     *
     * @param array $user User data array with 'id' and 'role' keys
     *
     * EXAMPLE:
     * ```php
     * $user = User::findByEmail($email);
     * if ($user && password_verify($password, $user['password_hash'])) {
     *     Auth::login($user);
     *     // User is now logged in
     * }
     * ```
     */
    public static function login(array $user): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'] ?? 'user';
    }

    /**
     * Log out the current user by clearing session data
     *
     * Properly destroys the session by:
     * 1. Clearing all session variables
     * 2. Deleting the session cookie from the browser
     * 3. Destroying the server-side session file
     *
     * EXAMPLE:
     * ```php
     * Auth::logout();
     * // User is now logged out
     * ```
     */
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

    /**
     * Check if a user is currently authenticated
     *
     * @return bool True if a user is logged in, false otherwise
     *
     * EXAMPLE:
     * ```php
     * if (Auth::check()) {
     *     echo "Welcome back!";
     * } else {
     *     echo "Please log in";
     * }
     * ```
     */
    public static function check(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        return isset($_SESSION['user_id']);
    }

    /**
     * Get the currently authenticated user's data
     *
     * Fetches fresh user data from the database on each call.
     * Returns null if no user is logged in.
     *
     * @return array|null User data array or null if not authenticated
     *
     * EXAMPLE:
     * ```php
     * $user = Auth::user();
     * if ($user) {
     *     echo "Hello, " . $user['name'];
     * }
     * ```
     */
    public static function user(): ?array
    {
        if (!self::check()) return null;
        return User::find((int)$_SESSION['user_id']);
    }

    /**
     * Get the current user's role
     *
     * @return string|null The role name ('admin', 'editor', 'user') or null if not authenticated
     *
     * EXAMPLE:
     * ```php
     * $role = Auth::role();
     * echo "Your role: " . ($role ?? 'guest');
     * ```
     */
    public static function role(): ?string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Check if the current user has one of the specified roles
     *
     * Performs strict comparison to ensure type safety.
     *
     * @param array $roles Array of role names to check against (e.g., ['admin', 'editor'])
     * @return bool True if user has any of the specified roles, false otherwise
     *
     * EXAMPLE:
     * ```php
     * // Check for admin access
     * if (Auth::authorize(['admin'])) {
     *     // Show admin panel
     * }
     *
     * // Check for admin or editor access
     * if (Auth::authorize(['admin', 'editor'])) {
     *     // Show content management
     * }
     * ```
     */
    public static function authorize(array $roles): bool
    {
        $r = self::role();
        return $r !== null && in_array($r, $roles, true);
    }
}


<?php
/**
 * Authentication Controller
 *
 * Handles user authentication operations including login, registration, and logout.
 * Implements secure password handling, CSRF protection, and session management.
 */

namespace App\Controllers;

use App\Controller;
use App\Models\User;
use App\Support\Auth;
use App\Support\Validator;

class AuthController extends Controller
{
    /**
     * Display the login form
     *
     * Route: GET /login
     */
    public function showLogin(): void
    {
        $this->render('auth/login', ['title' => 'Login']);
    }

    /**
     * Process login form submission
     *
     * Validates credentials, checks if user is active, and logs them in.
     * Redirects to intended URL or home page on success.
     *
     * Route: POST /login
     *
     * Security features:
     * - CSRF token validation
     * - Email/password validation
     * - Active user check
     * - Generic error messages (doesn't reveal if email exists)
     * - Intended URL redirect after login
     */
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
            foreach (Validator::flattenErrors($errors) as $e) $this->flash($e, 'is-danger');
            $this->redirect('/login');
        }

        $user = User::findByEmail(strtolower($data['email']));
        if (!$user || (int)($user['active'] ?? 1) !== 1 || !password_verify($data['password'], $user['password_hash'])) {
            $this->flash('Invalid credentials.', 'is-danger');
            $this->redirect('/login');
        }

        Auth::login($user);
        $dest = $_SESSION['intended'] ?? '/';
        unset($_SESSION['intended']);
        $this->flash('Welcome back!', 'is-success');
        $this->redirect($dest);
    }

    /**
     * Display the registration form
     *
     * Route: GET /register
     */
    public function showRegister(): void
    {
        $this->render('auth/register', ['title' => 'Register']);
    }

    /**
     * Process registration form submission
     *
     * Creates a new user account with validated data, hashes password,
     * and automatically logs the user in after successful registration.
     *
     * Route: POST /register
     *
     * Validation rules:
     * - Name: required, 2-100 characters
     * - Email: required, valid email, unique in database
     * - Password: required, min 8 characters
     * - Password confirmation: must match password
     *
     * Security features:
     * - CSRF token validation
     * - Email uniqueness check
     * - Password hashing with PASSWORD_DEFAULT
     * - Automatic login after registration
     */
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
            foreach (Validator::flattenErrors($errors) as $e) $this->flash($e, 'is-danger');
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

    /**
     * Log out the current user
     *
     * Destroys the session, clears session cookies, and redirects to home page.
     *
     * Route: POST /logout
     *
     * Security: CSRF token required (logout must be via POST, not GET)
     */
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
}


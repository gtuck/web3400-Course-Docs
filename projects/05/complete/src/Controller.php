<?php
/**
 * Base Controller Class
 *
 * PURPOSE:
 * Provides common functionality for all application controllers.
 * Acts as the foundation for all specific controllers (HomeController, ContactController, etc.)
 *
 * RESPONSIBILITIES:
 * - Initialize the view engine for rendering templates
 * - Share common variables across all views (site name, email, phone, etc.)
 * - Provide helper methods for rendering, flash messages, redirects, and CSRF protection
 * - Centralize common controller behavior to promote DRY principles
 *
 * INHERITANCE:
 * All application controllers should extend this class:
 * ```php
 * class HomeController extends Controller {
 *     public function index() {
 *         $this->render('home', ['data' => 'value']);
 *     }
 * }
 * ```
 */

namespace App;

use App\Support\View;

class Controller
{
    /**
     * @var View The view engine instance used for rendering templates
     */
    protected View $view;

    /**
     * Constructor: Initialize the view engine and share global variables
     *
     * PROCESS:
     * 1. Creates a View instance pointing to the Views directory
     * 2. Loads site-wide settings from environment variables
     * 3. Shares these settings with all views for consistent branding
     * 4. Generates and shares CSRF token
     *
     * SHARED VARIABLES:
     * - siteName: Website title (from SITE_NAME env var)
     * - siteEmail: Contact email (from SITE_EMAIL env var)
     * - sitePhone: Contact phone (from SITE_PHONE env var)
     * - csrfToken: CSRF protection token
     *
     * These variables are automatically available in all views
     */
    public function __construct()
    {
        // Point to the Views directory relative to this file
        $this->view = new View(__DIR__ . '/Views');

        // Load site‑wide configuration from environment variables with sensible defaults
        $siteName = $_ENV['SITE_NAME'] ?? 'My PHP Site';
        $siteEmail = $_ENV['SITE_EMAIL'] ?? 'email@website.com';
        $sitePhone = $_ENV['SITE_PHONE'] ?? '123-321-9876';

        // Make these variables available in all views, including CSRF token
        $this->view->share([
            'siteName' => $siteName,
            'siteEmail' => $siteEmail,
            'sitePhone' => $sitePhone,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Render a view and output it to the browser
     *
     * This is the primary method for displaying content to users.
     * It processes the view template and outputs the resulting HTML.
     *
     * @param string $view View file name (without .php extension)
     *                     Supports dot notation: 'layouts.main' or slash: 'layouts/main'
     * @param array $data Associative array of variables to pass to the view
     *
     * EXAMPLE:
     * ```php
     * // In a controller method
     * $this->render('contact', [
     *     'title' => 'Contact Us',
     *     'user' => $currentUser
     * ]);
     * ```
     *
     * The variables will be extracted and available in the view:
     * <?= $title ?> outputs "Contact Us"
     * <?= $user['name'] ?> outputs the user's name
     */
    protected function render(string $view, array $data = []): void
    {
        echo $this->view->render($view, $data);
    }

    /**
     * Add a flash message to the session for the next request
     *
     * Flash messages are one-time messages displayed after a redirect,
     * typically for success confirmations, errors, or warnings.
     *
     * @param string $text The message to display to the user
     * @param string $type CSS class for styling (default: 'is-info')
     *                     Bulma classes: 'is-success', 'is-warning', 'is-danger', 'is-info'
     *
     * USAGE PATTERN:
     * ```php
     * // After processing a form
     * Contact::create($data);
     * $this->flash('Message sent successfully!', 'is-success');
     * $this->redirect('/contact');
     * ```
     *
     * The message will be displayed on the next page load,
     * then automatically cleared from the session.
     *
     * EXAMPLE:
     * $this->flash('Invalid email address', 'is-danger');
     * $this->flash('Settings saved', 'is-success');
     */
    protected function flash(string $text, string $type = 'is-info'): void
    {
        // Ensure session is started
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Initialize messages array if it doesn't exist
        $_SESSION['messages'] = $_SESSION['messages'] ?? [];

        // Add the new message
        $_SESSION['messages'][] = compact('type', 'text');
    }

    /**
     * Redirect to a different URL with an HTTP status code
     *
     * This method terminates execution and sends the user to a different page.
     * Commonly used after form submissions (POST-Redirect-GET pattern).
     *
     * @param string $path URL path to redirect to (e.g., '/contact', '/users')
     * @param int $status HTTP status code (default: 303 See Other)
     *                    303 = See Other (recommended for POST-redirect-GET)
     *                    301 = Moved Permanently
     *                    302 = Found (temporary redirect)
     *
     * EXAMPLE POST-REDIRECT-GET PATTERN:
     * ```php
     * public function submit() {
     *     // Process form data
     *     User::create($_POST);
     *
     *     // Add success message
     *     $this->flash('Account created!', 'is-success');
     *
     *     // Redirect to prevent duplicate submissions
     *     $this->redirect('/users');
     * }
     * ```
     *
     * NOTE: This method calls exit() - no code after it will execute
     */
    protected function redirect(string $path, int $status = 303): void
    {
        header('Location: ' . $path, true, $status);
        exit;
    }

    /**
     * Get or generate a CSRF token for the current session
     *
     * CSRF (Cross-Site Request Forgery) protection prevents malicious websites
     * from submitting forms on behalf of authenticated users.
     *
     * This method generates a cryptographically secure random token and stores
     * it in the session. The same token is returned on subsequent calls.
     *
     * @return string The CSRF token (64 character hexadecimal string)
     *
     * USAGE IN VIEWS:
     * ```php
     * <form method="post">
     *     <input type="hidden" name="csrf_token" value="<?= $this->e($csrfToken) ?>">
     *     <!-- other form fields -->
     * </form>
     * ```
     *
     * USAGE IN CONTROLLERS:
     * ```php
     * public function submit() {
     *     if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
     *         throw new \Exception('Invalid CSRF token');
     *     }
     *     // Process form...
     * }
     * ```
     */
    protected function csrfToken(): string
    {
        // Ensure session is started
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Generate token if it doesn't exist
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Validate a CSRF token against the session token
     *
     * Uses timing-safe comparison to prevent timing attacks.
     *
     * @param string $token The token to validate (from form submission)
     * @return bool True if token is valid, false otherwise
     *
     * SECURITY NOTE:
     * Always validate CSRF tokens for state-changing operations (POST, PUT, DELETE).
     * GET requests should never modify data, so they don't need CSRF protection.
     *
     * EXAMPLE:
     * ```php
     * public function submit() {
     *     // Validate CSRF token first
     *     if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
     *         $this->flash('Security token validation failed', 'is-danger');
     *         $this->redirect('/contact');
     *     }
     *
     *     // Token is valid, proceed with form processing
     *     Contact::create($_POST);
     * }
     * ```
     */
    protected function validateCsrf(string $token): bool
    {
        // Ensure session is started
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Use timing-safe comparison to prevent timing attacks
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}

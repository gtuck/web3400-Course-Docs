<?php
// filepath: projects/05/src/Controller.php
namespace App;

use App\Support\View;

class Controller
{
    protected View $view;

    public function __construct()
    {
        // Point to the Views directory relative to this file
        $this->view = new View(__DIR__ . '/Views');

        // Share site‑wide variables
        $siteName = $_ENV['SITE_NAME'] ?? 'My PHP Site';
        $siteEmail = $_ENV['SITE_EMAIL'] ?? 'email@website.com';
        $sitePhone = $_ENV['SITE_PHONE'] ?? '123-321-9876';

        $this->view->share([
            'siteName' => $siteName,
            'siteEmail' => $siteEmail,
            'sitePhone' => $sitePhone,
            'csrfToken' => $this->csrfToken(),  // NEW: Share CSRF token
        ]);
    }

    protected function render(string $view, array $data = []): void
    {
        echo $this->view->render($view, $data);
    }

    // Flash a message for next request
    protected function flash(string $text, string $type = 'is-info'): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['messages'] = $_SESSION['messages'] ?? [];
        $_SESSION['messages'][] = compact('type', 'text');
    }

    // Redirect with default 303 (PRG)
    protected function redirect(string $path, int $status = 303): void
    {
        header('Location: ' . $path, true, $status);
        exit;
    }

    // NEW: Generate or retrieve CSRF token
    protected function csrfToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // NEW: Validate CSRF token (timing-safe comparison)
    protected function validateCsrf(string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

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

    // Shared helper: create URL-friendly slugs
    protected function slugify(string $value): string
    {
        $v = strtolower(trim($value));
        $v = preg_replace('~[^a-z0-9]+~', '-', $v) ?? '';
        $v = trim($v, '-');
        return $v ?: uniqid('post-');
    }
}

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
        // Optionally share site‑wide variables
        $siteName = $_ENV['SITE_NAME'] ?? 'My PHP Site';
        $siteEmail = $_ENV['SITE_EMAIL'] ?? 'email@website.com';
        $sitePhone = $_ENV['SITE_PHONE'] ?? '123-321-9876';
        $this->view->share([
            'siteName' => $siteName,
            'siteEmail' => $siteEmail,
            'sitePhone' => $sitePhone,
        ]);
    }

    protected function render(string $view, array $data = []): void
    {
        echo $this->view->render($view, $data);
    }

    /**
     * Add a flash message to the session for the next request.
     */
    protected function flash(string $text, string $type = 'is-info'): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['messages'] = $_SESSION['messages'] ?? [];
        $_SESSION['messages'][] = compact('type', 'text');
    }

    /**
     * Redirect to a path with an HTTP status code (default 303 See Other).
     */
    protected function redirect(string $path, int $status = 303): void
    {
        header('Location: ' . $path, true, $status);
        exit;
    }
}

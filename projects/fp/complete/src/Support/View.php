<?php
// filepath: projects/05/src/Support/View.php
namespace App\Support;

final class View
{
    private string $basePath;
    private string $ext;
    private ?string $layout = null;
    private array $sections = [];
    private array $sectionStack = [];
    private array $shared = [];

    public function __construct(string $basePath, string $ext = 'php', array $shared = [])
    {
        $this->basePath = rtrim($basePath, '/');
        $this->ext = ltrim($ext, '.');
        $this->shared = $shared;
    }

    public function share(array $vars): void
    {
        $this->shared = $vars + $this->shared;
    }

    public function layout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function start(string $section): void
    {
        $this->sectionStack[] = $section;
        ob_start();
    }

    public function end(): void
    {
        $section = array_pop($this->sectionStack);
        if ($section === null) {
            throw new \RuntimeException('No active section to end().');
        }
        $this->sections[$section] = ($this->sections[$section] ?? '') . ob_get_clean();
    }

    public function section(string $name, string $default = ''): void
    {
        echo $this->sections[$name] ?? $default;
    }

    public function insert(string $view, array $data = []): void
    {
        $this->includeFile($this->resolve($view), $data);
    }

    public function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function render(string $view, array $data = []): string
    {
        // Reset per-render state
        $this->layout = null;
        $this->sections = [];
        $this->sectionStack = [];

        // Render the view first
        ob_start();
        $this->includeFile($this->resolve($view), $data);
        $viewOutput = ob_get_clean();

        // If a layout was declared, make the full view output the default `content`
        if ($this->layout) {
            if (!isset($this->sections['content'])) {
                $this->sections['content'] = $viewOutput;
            }

            ob_start();
            $this->includeFile($this->resolve($this->layout), $data);
            return ob_get_clean();
        }

        return $viewOutput;
    }

    private function resolve(string $view): string
    {
        // Support dotted or slashed notation: e.g., 'layouts.main' or 'layouts/main'
        $clean = str_replace(['\\\\', '..'], ['/', ''], $view);
        $clean = str_replace('.', '/', $clean);
        $path = $this->basePath . '/' . $clean . '.' . $this->ext;
        if (!is_file($path)) {
            throw new \RuntimeException("View not found: {$path}");
        }
        return $path;
    }

    private function includeFile(string $path, array $vars): void
    {
        // Shared first, then per-render data (per-render wins on key conflicts)
        extract($this->shared, EXTR_SKIP);
        extract($vars, EXTR_SKIP);
        include $path; // `$this` is available inside included templates
    }

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
}
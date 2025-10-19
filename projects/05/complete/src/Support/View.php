<?php
/**
 * View Class - Vanilla PHP Template Engine
 *
 * PURPOSE:
 * A lightweight, dependency-free templating system inspired by Laravel Blade.
 * Uses output buffering and plain PHP to provide layout inheritance, named sections,
 * partials, and safe output escaping without external dependencies.
 *
 * FEATURES:
 * - Layout inheritance (master/child view pattern)
 * - Named sections for content organization
 * - Reusable partials (includes)
 * - Shared data available across all views
 * - XSS protection via escape helper
 * - Dot or slash notation for view paths
 *
 * ARCHITECTURE:
 * Uses output buffering (ob_start/ob_get_clean) to capture template output,
 * allowing views to declare layouts and sections before rendering.
 *
 * USAGE EXAMPLE:
 * ```php
 * // In Controller
 * $view = new View(__DIR__ . '/Views');
 * $view->share(['siteName' => 'My App']);
 * echo $view->render('home', ['posts' => $posts]);
 *
 * // In home.php view
 * <?php $this->layout('layouts/main'); ?>
 * <?php $this->start('content'); ?>
 *   <h1><?= $this->e($siteName) ?></h1>
 * <?php $this->end(); ?>
 * ```
 */

namespace App\Support;

final class View
{
    /** @var string Base directory for all view files */
    private string $basePath;

    /** @var string File extension for view files (default: 'php') */
    private string $ext;

    /** @var string|null The layout template to wrap this view in */
    private ?string $layout = null;

    /** @var array<string, string> Named sections captured during rendering */
    private array $sections = [];

    /** @var array<string> Stack of currently active sections (for nested sections) */
    private array $sectionStack = [];

    /** @var array<string, mixed> Data shared across all views */
    private array $shared = [];

    /**
     * Create a new View instance
     *
     * @param string $basePath Absolute path to views directory (e.g., __DIR__ . '/Views')
     * @param string $ext File extension (default: 'php')
     * @param array $shared Initial shared data available in all views
     *
     * EXAMPLE:
     * ```php
     * $view = new View(__DIR__ . '/Views', 'php', [
     *     'appName' => 'My Application'
     * ]);
     * ```
     */
    public function __construct(string $basePath, string $ext = 'php', array $shared = [])
    {
        // Normalize paths: remove trailing slashes, leading dots from extension
        $this->basePath = rtrim($basePath, '/');
        $this->ext = ltrim($ext, '.');
        $this->shared = $shared;
    }

    /**
     * Share data with all views
     *
     * Shared data is available in every view, layout, and partial.
     * New values take precedence over previously shared values.
     *
     * @param array $vars Associative array of variables to share
     *
     * EXAMPLE:
     * ```php
     * $view->share([
     *     'siteName' => 'My Site',
     *     'user' => $currentUser,
     *     'csrfToken' => bin2hex(random_bytes(32))
     * ]);
     *
     * // Now available in all views as $siteName, $user, $csrfToken
     * ```
     *
     * USE CASES:
     * - Site-wide settings (name, logo, etc.)
     * - Current user information
     * - CSRF tokens
     * - Flash messages
     */
    public function share(array $vars): void
    {
        // New values overwrite old (left side wins in array union)
        $this->shared = $vars + $this->shared;
    }

    /**
     * Declare which layout this view should use
     *
     * Call this at the top of your view file to wrap it in a layout.
     * The view's content will be available as the 'content' section in the layout.
     *
     * @param string $layout Layout name (e.g., 'layouts/main' or 'layouts.main')
     *
     * EXAMPLE:
     * ```php
     * // At top of contact.php
     * <?php $this->layout('layouts/main'); ?>
     * <?php $this->start('content'); ?>
     *   <h1>Contact Us</h1>
     * <?php $this->end(); ?>
     * ```
     *
     * NOTE: This method is called from within view templates, not controllers.
     */
    public function layout(string $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * Start capturing output for a named section
     *
     * Everything echoed between start() and end() is captured and stored
     * in the named section for later output via section().
     *
     * @param string $section Section name (e.g., 'content', 'sidebar', 'scripts')
     *
     * EXAMPLE:
     * ```php
     * <?php $this->start('sidebar'); ?>
     *   <nav>...</nav>
     * <?php $this->end(); ?>
     *
     * // Later, in layout:
     * <?php $this->section('sidebar'); ?>
     * ```
     *
     * ADVANCED:
     * Sections can be nested and appended to by calling start() multiple times.
     */
    public function start(string $section): void
    {
        // Push section name onto stack and start buffering
        $this->sectionStack[] = $section;
        ob_start();
    }

    /**
     * Stop capturing output and save to the current section
     *
     * Must be called after start() to close the section.
     *
     * @throws \RuntimeException If called without a matching start()
     *
     * EXAMPLE:
     * ```php
     * <?php $this->start('content'); ?>
     *   <p>Hello World</p>
     * <?php $this->end(); ?>
     * ```
     */
    public function end(): void
    {
        // Pop the most recent section from the stack
        $section = array_pop($this->sectionStack);

        if ($section === null) {
            throw new \RuntimeException('No active section to end().');
        }

        // Capture buffered output and append to section (allows multiple starts for same section)
        $this->sections[$section] = ($this->sections[$section] ?? '') . ob_get_clean();
    }

    /**
     * Output a section's content
     *
     * Typically called in layouts to inject content from child views.
     *
     * @param string $name Section name
     * @param string $default Fallback content if section doesn't exist
     *
     * EXAMPLE:
     * ```php
     * // In layouts/main.php
     * <main>
     *   <?php $this->section('content', '<p>No content</p>'); ?>
     * </main>
     * ```
     */
    public function section(string $name, string $default = ''): void
    {
        echo $this->sections[$name] ?? $default;
    }

    /**
     * Include a partial view (reusable component)
     *
     * Partials are small, reusable view fragments like headers, footers, navbars.
     * They have access to shared data plus any data passed to insert().
     *
     * @param string $view Partial name (e.g., 'partials/nav', 'partials.header')
     * @param array $data Additional data for this partial
     *
     * EXAMPLE:
     * ```php
     * // In layout
     * <?php $this->insert('partials/nav'); ?>
     * <?php $this->insert('partials/head', ['title' => $title . ' - ' . $siteName]); ?>
     *
     * // partials/nav.php can access $siteName (shared) and any passed data
     * ```
     *
     * USE CASES:
     * - Header/footer/nav components
     * - Flash message displays
     * - Reusable widgets
     */
    public function insert(string $view, array $data = []): void
    {
        $this->includeFile($this->resolve($view), $data);
    }

    /**
     * Escape a value for safe HTML output (XSS protection)
     *
     * Converts special characters to HTML entities to prevent XSS attacks.
     * Always use this when outputting user input or database values.
     *
     * @param mixed $value Value to escape (will be cast to string)
     * @return string HTML-safe string
     *
     * EXAMPLE:
     * ```php
     * <h2><?= $this->e($post['title']) ?></h2>
     * <p><?= $this->e($comment['body']) ?></p>
     * <input value="<?= $this->e($old['email'] ?? '') ?>">
     * ```
     *
     * FLAGS:
     * - ENT_QUOTES: Escapes both single and double quotes
     * - ENT_SUBSTITUTE: Replace invalid code points with Unicode replacement character
     * - UTF-8: Character encoding
     *
     * SECURITY:
     * This is your primary defense against XSS attacks. Use it for ALL untrusted data.
     */
    public function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Render a view and return the output as a string
     *
     * This is the main method called by controllers to generate HTML.
     *
     * PROCESS:
     * 1. Reset render state (layout, sections, stack)
     * 2. Render the view file (may call layout(), start(), end())
     * 3. If a layout was declared, wrap view in layout
     * 4. Return final HTML string
     *
     * @param string $view View name (e.g., 'home', 'contact', 'users/profile')
     * @param array $data Variables to pass to the view
     * @return string Rendered HTML
     *
     * @throws \RuntimeException If view file not found
     *
     * EXAMPLE:
     * ```php
     * // In controller
     * $html = $view->render('posts/show', [
     *     'title' => 'My Post',
     *     'post' => $post,
     *     'comments' => $comments
     * ]);
     * echo $html;
     * ```
     *
     * FLOW:
     * 1. Controller calls render('contact', ['old' => []])
     * 2. contact.php declares layout('layouts/main')
     * 3. contact.php defines section('content')
     * 4. layouts/main.php calls section('content')
     * 5. Final HTML returned to controller
     */
    public function render(string $view, array $data = []): string
    {
        // Reset per-render state to avoid pollution between renders
        $this->layout = null;
        $this->sections = [];
        $this->sectionStack = [];

        // Render the view first (captures any layout() and section() calls)
        ob_start();
        $this->includeFile($this->resolve($view), $data);
        $viewOutput = ob_get_clean();

        // If view declared a layout, wrap it
        if ($this->layout) {
            // If view didn't define a 'content' section explicitly,
            // make the entire view output the 'content' section
            if (!isset($this->sections['content'])) {
                $this->sections['content'] = $viewOutput;
            }

            // Render the layout (which will call section('content'))
            ob_start();
            $this->includeFile($this->resolve($this->layout), $data);
            return ob_get_clean();
        }

        // No layout declared, return view as-is
        return $viewOutput;
    }

    /**
     * Resolve a view name to an absolute file path
     *
     * Supports both dot notation ('layouts.main') and slash notation ('layouts/main').
     * Validates file exists before returning path.
     *
     * @param string $view View name in dot or slash notation
     * @return string Absolute path to view file
     *
     * @throws \RuntimeException If view file not found
     *
     * SECURITY:
     * - Prevents directory traversal by removing '..' sequences
     * - Prevents Windows path issues by replacing backslashes
     *
     * EXAMPLES:
     * - 'home' → '/path/to/views/home.php'
     * - 'layouts.main' → '/path/to/views/layouts/main.php'
     * - 'partials/nav' → '/path/to/views/partials/nav.php'
     */
    private function resolve(string $view): string
    {
        // Security: prevent directory traversal and normalize slashes
        $clean = str_replace(['\\\\', '..'], ['/', ''], $view);

        // Convert dot notation to slashes: 'layouts.main' → 'layouts/main'
        $clean = str_replace('.', '/', $clean);

        // Build full path
        $path = $this->basePath . '/' . $clean . '.' . $this->ext;

        // Validate file exists
        if (!is_file($path)) {
            throw new \RuntimeException("View not found: {$path}");
        }

        return $path;
    }

    /**
     * Include a view file with extracted variables
     *
     * This method makes variables available to templates via extract().
     * Inside templates, $this refers to this View instance, allowing
     * calls to layout(), start(), end(), section(), insert(), e().
     *
     * @param string $path Absolute path to view file
     * @param array $vars Variables to make available in the view
     *
     * VARIABLE PRECEDENCE:
     * 1. Shared data (lowest priority)
     * 2. Per-render data (highest priority)
     *
     * EXTRACT FLAGS:
     * - EXTR_SKIP: Don't overwrite existing variables (gives precedence to shared/per-render)
     *
     * EXAMPLE:
     * If shared has ['user' => $sharedUser] and $vars has ['user' => $localUser],
     * the view receives $localUser (per-render wins).
     */
    private function includeFile(string $path, array $vars): void
    {
        // Extract shared variables first (can be overridden by per-render)
        extract($this->shared, EXTR_SKIP);

        // Extract per-render variables (take precedence)
        extract($vars, EXTR_SKIP);

        // Include the file - `$this` is available inside for calling View methods
        include $path;
    }
}

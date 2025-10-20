<?php

namespace App;

/**
 * Controller - Base controller class for all application controllers
 *
 * Provides common functionality for all controllers, including view rendering.
 * All application controllers should extend this base class to inherit
 * shared functionality.
 *
 * Example usage:
 *
 * namespace App\Controllers;
 *
 * use App\Controller;
 * use App\Models\User;
 *
 * class UserController extends Controller
 * {
 *     public function index()
 *     {
 *         $users = User::all(10);
 *         $this->render('users/index', ['users' => $users]);
 *     }
 *
 *     public function show()
 *     {
 *         $userId = $_GET['id'] ?? null;
 *         $user = User::find($userId);
 *
 *         if (!$user) {
 *             $this->render('errors/404', ['message' => 'User not found']);
 *             return;
 *         }
 *
 *         $this->render('users/show', ['user' => $user]);
 *     }
 * }
 */
class Controller
{
    /**
     * Render a view template with data
     *
     * Loads a PHP view file from the Views directory and passes data to it.
     * The data array is extracted into individual variables that are accessible
     * in the view template.
     *
     * SECURITY NOTE: Be careful with the data passed to views. Always escape
     * output in views using htmlspecialchars() to prevent XSS attacks.
     *
     * @param string $view Path to view file relative to Views directory (without .php extension)
     * @param array $data Associative array of data to pass to the view
     * @return void
     *
     * Example:
     * // In a controller action method:
     *
     * // 1. Render a simple view with no data
     * $this->render('home/index');
     *
     * // 2. Render a view with data
     * $this->render('users/profile', [
     *     'username' => 'johndoe',
     *     'email' => 'john@example.com'
     * ]);
     *
     * // In Views/users/profile.php, you can access:
     * // <?php echo htmlspecialchars($username); ?>
     * // <?php echo htmlspecialchars($email); ?>
     *
     * // 3. Render a list view
     * $users = User::all(10);
     * $this->render('users/index', [
     *     'users' => $users,
     *     'title' => 'User List'
     * ]);
     *
     * // In Views/users/index.php:
     * // <h1><?php echo htmlspecialchars($title); ?></h1>
     * // <?php foreach ($users as $user): ?>
     * //     <li><?php echo htmlspecialchars($user['name']); ?></li>
     * // <?php endforeach; ?>
     *
     * // 4. Nested view paths (using subdirectories)
     * $this->render('admin/users/edit', ['user' => $user]);
     * // Loads: Views/admin/users/edit.php
     *
     * // 5. Passing multiple data types
     * $this->render('dashboard', [
     *     'user' => $currentUser,           // array
     *     'stats' => $statistics,            // array
     *     'isAdmin' => true,                 // boolean
     *     'notificationCount' => 5,          // integer
     *     'message' => 'Welcome back!'       // string
     * ]);
     *
     * // 6. Error handling example
     * if (!$product) {
     *     $this->render('errors/404', [
     *         'message' => 'Product not found',
     *         'errorCode' => 404
     *     ]);
     *     return;
     * }
     *
     * View file organization:
     * Views/
     *   ├── home/
     *   │   └── index.php
     *   ├── users/
     *   │   ├── index.php
     *   │   ├── show.php
     *   │   └── edit.php
     *   ├── errors/
     *   │   └── 404.php
     *   └── layouts/
     *       └── main.php
     *
     * Best practices:
     * - Always escape output in views: htmlspecialchars($var)
     * - Keep business logic in controllers, not views
     * - Use descriptive variable names in $data array
     * - Organize views in subdirectories by resource (users/, products/, etc.)
     */
    protected function render($view, $data = [])
    {
        extract($data);
        include "Views/$view.php";
    }
}



<?php
/**
 * Users Controller (Admin)
 *
 * Manages user administration operations including listing, creating,
 * editing, and deactivating user accounts.
 *
 * All methods require admin role via requireRole('admin').
 *
 * Security features:
 * - Prevents removing the last admin
 * - Uses adminCreate/adminUpdate to bypass mass assignment protection
 * - CSRF protection on all state-changing operations
 */

namespace App\Controllers;

use App\Controller;
use App\Models\User;
use App\Support\Validator;

class UsersController extends Controller
{
    /**
     * Display a list of all users
     *
     * Shows all users ordered by creation date (newest first).
     *
     * Route: GET /admin/users
     * Auth: Admin only
     */
    public function index(): void
    {
        $this->requireRole('admin');
        $users = User::all(orderBy: 'created_at DESC');
        $this->render('admin/users/index', [
            'title' => 'Users',
            'users' => $users,
        ]);
    }

    /**
     * Display the create user form
     *
     * Route: GET /admin/users/create
     * Auth: Admin only
     */
    public function create(): void
    {
        $this->requireRole('admin');
        $this->render('admin/users/create', [
            'title' => 'Create User',
        ]);
    }

    /**
     * Process create user form submission
     *
     * Creates a new user with admin-level control (can set role).
     *
     * Route: POST /admin/users
     * Auth: Admin only
     *
     * Validation rules:
     * - Name: required, 2-100 characters
     * - Email: required, valid email, unique
     * - Role: required, must be admin/editor/user
     * - Password: required, min 8 characters
     *
     * Security:
     * - Uses adminCreate() to bypass mass assignment protection
     * - Password hashing with PASSWORD_DEFAULT
     */
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

    /**
     * Display the edit user form
     *
     * Route: GET /admin/users/edit?id=123
     * Auth: Admin only
     */
    public function edit(): void
    {
        $this->requireRole('admin');
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0 || !($user = User::find($id))) {
            $this->flash('User not found.', 'is-danger');
            $this->redirect('/admin/users');
        }
        $this->render('admin/users/edit', [
            'title' => 'Edit User',
            'user' => $user,
        ]);
    }

    /**
     * Process edit user form submission
     *
     * Updates user information including role and active status.
     * Prevents removing or deactivating the last admin.
     *
     * Route: POST /admin/users/update
     * Auth: Admin only
     *
     * Validation rules:
     * - Name: required, 2-100 characters
     * - Email: required, valid email, unique (except current user)
     * - Role: required, must be admin/editor/user
     * - Active: checkbox (1 if checked, 0 if unchecked)
     *
     * Security:
     * - Last admin protection (can't demote or deactivate)
     * - Uses adminUpdate() to bypass mass assignment protection
     */
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

        // Prevent removing last admin
        $demotingAdmin = ($user['role'] === 'admin') && ($data['role'] !== 'admin' || $data['active'] !== 1);
        if ($demotingAdmin && User::countAdmins() <= 1) {
            $this->flash('Cannot remove the last remaining admin.', 'is-danger');
            $this->redirect('/admin/users/edit?id=' . $id);
        }

        User::adminUpdate($id, $data);
        $this->flash('User updated.', 'is-success');
        $this->redirect('/admin/users');
    }

    /**
     * Deactivate a user account
     *
     * Sets user's active status to 0 instead of deleting the record.
     * Prevents deactivating the last admin.
     *
     * Route: POST /admin/users/delete
     * Auth: Admin only
     *
     * Security:
     * - Last admin protection (can't deactivate last admin)
     * - CSRF token validation
     * - Soft delete approach (deactivate rather than delete)
     */
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


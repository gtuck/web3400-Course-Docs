<?php
// filepath: projects/06/src/Controllers/Admin/UsersController.php
namespace App\Controllers\Admin;

use App\Controller;
use App\Models\User;
use App\Support\Validator;

class UsersController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireRole('admin');
    }

    public function index(): void
    {
        $users = \App\Models\User::all(limit: 200, offset: 0, orderBy: '`id` DESC');
        $this->render('admin/users/index', ['title' => 'Users', 'users' => $users]);
    }

    public function create(): void
    {
        $this->render('admin/users/create', ['title' => 'Create User']);
    }

    public function store(): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect('/admin/users/create');
        }
        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $role = $_POST['role'] ?? 'user';
        $password = $_POST['password'] ?? '';

        $errors = \App\Support\Validator::validate(
            compact('name', 'email', 'role', 'password'),
            [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255',
                'role' => 'required|in:admin,editor,user',
                'password' => 'required|min:8',
            ]
        );
        if (\App\Models\User::existsBy('email', $email)) {
            $errors['email'][] = 'Email is already registered.';
        }
        if (!empty($errors)) {
            foreach (\App\Support\Validator::flattenErrors($errors) as $m) {
                $this->flash($m, 'is-warning');
            }
            $this->redirect('/admin/users/create');
        }

        \App\Models\User::create([
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'is_active' => 1,
        ]);
        $this->flash('User created.', 'is-success');
        $this->redirect('/admin/users');
    }

    public function edit(int $id): void
    {
        $user = \App\Models\User::find($id);
        if (!$user) {
            $this->flash('User not found.', 'is-warning');
            $this->redirect('/admin/users');
        }
        $this->render('admin/users/edit', ['title' => 'Edit User', 'user' => $user]);
    }

    public function update(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect("/admin/users/{$id}/edit");
        }
        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $role = $_POST['role'] ?? 'user';
        $is_active = (int)($_POST['is_active'] ?? 1);

        $errors = \App\Support\Validator::validate(
            compact('name', 'email', 'role'),
            [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255',
                'role' => 'required|in:admin,editor,user',
            ]
        );
        if (\App\Models\User::existsBy('email', $email, $id)) {
            $errors['email'][] = 'That email is already in use.';
        }
        if (!empty($errors)) {
            foreach (\App\Support\Validator::flattenErrors($errors) as $m) {
                $this->flash($m, 'is-warning');
            }
            $this->redirect("/admin/users/{$id}/edit");
        }

        \App\Models\User::update($id, compact('name', 'email', 'role', 'is_active'));
        $this->flash('User updated.', 'is-success');
        $this->redirect('/admin/users');
    }

    public function updateRole(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect('/admin/users');
        }
        $role = $_POST['role'] ?? 'user';
        $errs = \App\Support\Validator::validate(['role' => $role], ['role' => 'required|in:admin,editor,user']);
        if (!empty($errs)) {
            foreach (\App\Support\Validator::flattenErrors($errs) as $m) {
                $this->flash($m, 'is-warning');
            }
            $this->redirect('/admin/users');
        }
        \App\Models\User::update($id, ['role' => $role]);
        $this->flash('Role updated.', 'is-success');
        $this->redirect('/admin/users');
    }

    public function updateActive(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect('/admin/users');
        }
        $isActive = isset($_POST['is_active']) ? 1 : 0; // checkbox presence
        \App\Models\User::update($id, ['is_active' => $isActive]);
        $this->flash('User status updated.', 'is-info');
        $this->redirect('/admin/users');
    }
}

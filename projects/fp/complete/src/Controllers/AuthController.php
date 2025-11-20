<?php
// filepath: projects/06/src/Controllers/AuthController.php
namespace App\Controllers;

use App\Controller;
use App\Models\User;
use App\Support\Validator;

class AuthController extends Controller
{
    public function showRegister(): void
    {
        $this->render('auth/register', ['title' => 'Register']);
    }

    public function register(): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect('/register');
        }

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'email' => strtolower(trim($_POST['email'] ?? '')),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
        ];

        $errors = \App\Support\Validator::validate($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:8',
        ]);

        if ($data['password'] !== $data['password_confirm']) {
            $errors['password'][] = 'Password confirmation does not match.';
        }

        // Unique email
        if (\App\Models\User::existsBy('email', $data['email'])) {
            $errors['email'][] = 'Email is already registered.';
        }

        if (!empty($errors)) {
            foreach (\App\Support\Validator::flattenErrors($errors) as $m) {
                $this->flash($m, 'is-warning');
            }
            $this->render('auth/register', ['title' => 'Register', 'old' => $data]);
        }

        $id = \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => 'user',
            'is_active' => 1,
        ]);

        $user = \App\Models\User::find($id);
        $this->loginUser($user);
        $this->flash('Welcome, your account has been created!', 'is-success');
        $this->redirect('/profile');
    }

    public function showLogin(): void
    {
        $this->render('auth/login', ['title' => 'Login']);
    }

    public function login(): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect('/login');
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        $errors = \App\Support\Validator::validate(compact('email', 'password'), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (!empty($errors)) {
            foreach (\App\Support\Validator::flattenErrors($errors) as $m) {
                $this->flash($m, 'is-warning');
            }
            $this->redirect('/login');
        }

        $user = \App\Models\User::firstBy('email', $email);
        if (!$user || !$user['is_active']) {
            $this->flash('Invalid credentials.', 'is-danger');
            $this->redirect('/login');
        }

        if (!password_verify($password, $user['password_hash'])) {
            $this->flash('Invalid credentials.', 'is-danger');
            $this->redirect('/login');
        }

        $this->loginUser($user);
        $this->flash('Welcome back!', 'is-success');

        // Redirect admins to dashboard, regular users to home
        if ($user['role'] === 'admin') {
            $this->redirect('/admin/dashboard');
        } else {
            $this->redirect('/');
        }
    }

    public function logout(): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect('/');
        }
        $this->logoutUser();
        $this->flash('You have been logged out.', 'is-info');
        $this->redirect('/');
    }
}

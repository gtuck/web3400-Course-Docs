<?php
/**
 * Profile Controller
 *
 * Manages authenticated user profile operations including viewing profile,
 * updating personal information, and changing passwords.
 *
 * All methods require authentication via requireAuth().
 */

namespace App\Controllers;

use App\Controller;
use App\Models\User;
use App\Support\Auth;
use App\Support\Validator;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile
     *
     * Shows user information and a password change form.
     *
     * Route: GET /profile
     * Auth: Required
     */
    public function show(): void
    {
        $this->requireAuth();
        $this->render('profile/show', [
            'title' => 'Your Profile',
            'user' => Auth::user(),
        ]);
    }

    /**
     * Display the profile edit form
     *
     * Route: GET /profile/edit
     * Auth: Required
     */
    public function edit(): void
    {
        $this->requireAuth();
        $this->render('profile/edit', [
            'title' => 'Edit Profile',
            'user' => Auth::user(),
        ]);
    }

    /**
     * Process profile update form submission
     *
     * Updates user's name and email with validation.
     * Email uniqueness check ignores current user's ID.
     *
     * Route: POST /profile
     * Auth: Required
     *
     * Validation rules:
     * - Name: required, 2-100 characters
     * - Email: required, valid email, unique (except current user)
     *
     * Security:
     * - CSRF token validation
     * - Cannot update role or active status (mass assignment protection)
     */
    public function update(): void
    {
        $this->requireAuth();
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid security token.', 'is-danger');
            $this->redirect('/profile/edit');
        }
        $user = Auth::user();
        $data = [
            'name' => trim((string)($_POST['name'] ?? '')),
            'email' => strtolower(trim((string)($_POST['email'] ?? ''))),
        ];
        $rules = [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . $user['id'],
        ];
        $errors = Validator::validate($data, $rules);
        if (!empty($errors)) {
            foreach (Validator::flattenErrors($errors) as $e) $this->flash($e, 'is-danger');
            $this->redirect('/profile/edit');
        }
        User::update((int)$user['id'], $data);
        $this->flash('Profile updated.', 'is-success');
        $this->redirect('/profile');
    }

    /**
     * Process password change form submission
     *
     * Updates user's password after verifying current password.
     *
     * Route: POST /profile/password
     * Auth: Required
     *
     * Validation rules:
     * - Current password: required, must match existing password
     * - New password: required, min 8 characters
     * - Password confirmation: must match new password
     *
     * Security:
     * - CSRF token validation
     * - Current password verification
     * - Password hashing with PASSWORD_DEFAULT
     */
    public function updatePassword(): void
    {
        $this->requireAuth();
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid security token.', 'is-danger');
            $this->redirect('/profile');
        }
        $user = Auth::user();
        $data = [
            'current_password' => (string)($_POST['current_password'] ?? ''),
            'password' => (string)($_POST['password'] ?? ''),
            'password_confirm' => (string)($_POST['password_confirm'] ?? ''),
        ];
        $errors = Validator::validate($data, [
            'current_password' => 'required',
            'password' => 'required|min:8|max:255',
            'password_confirm' => 'required|same:password',
        ]);
        if (!empty($errors)) {
            foreach (Validator::flattenErrors($errors) as $e) $this->flash($e, 'is-danger');
            $this->redirect('/profile');
        }
        if (!password_verify($data['current_password'], $user['password_hash'])) {
            $this->flash('Current password is incorrect.', 'is-danger');
            $this->redirect('/profile');
        }
        User::update((int)$user['id'], [
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
        $this->flash('Password updated.', 'is-success');
        $this->redirect('/profile');
    }
}


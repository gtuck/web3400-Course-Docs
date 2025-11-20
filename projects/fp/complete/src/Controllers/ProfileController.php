<?php
// filepath: projects/06/src/Controllers/ProfileController.php
namespace App\Controllers;

use App\Controller;
use App\Models\User;
use App\Models\PostLike;
use App\Models\PostFavorite;
use App\Models\Comment;
use App\Support\Validator;

class ProfileController extends Controller
{
    public function show(): void
    {
        $this->requireAuth();
        $user = $this->user();
        $userId = (int)($user['id'] ?? 0);

        // Posts liked by the user
        $likedPosts = PostLike::postsLikedByUser($userId);

        // Posts favorited by the user
        $favoritedPosts = PostFavorite::postsFavoritedByUser($userId);

        // Posts the user has commented on (distinct)
        $commentedPosts = Comment::postsCommentedByUser($userId);

        $this->render('profile/show', [
            'title' => 'Your Profile',
            'user' => $user,
            'likedPosts' => $likedPosts,
            'favoritedPosts' => $favoritedPosts,
            'commentedPosts' => $commentedPosts,
        ]);
    }

    public function edit(): void
    {
        $this->requireAuth();
        $this->render('profile/edit', ['title' => 'Edit Profile', 'user' => $this->user()]);
    }

    public function update(): void
    {
        $this->requireAuth();
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect('/profile/edit');
        }
        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $id = (int)($this->user()['id'] ?? 0);

        $errors = \App\Support\Validator::validate(compact('name', 'email'), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
        ]);
        if (\App\Models\User::existsBy('email', $email, $id)) {
            $errors['email'][] = 'That email is already in use.';
        }
        if (!empty($errors)) {
            foreach (\App\Support\Validator::flattenErrors($errors) as $m) {
                $this->flash($m, 'is-warning');
            }
            $this->redirect('/profile/edit');
        }
        \App\Models\User::update($id, compact('name', 'email'));
        $this->flash('Profile updated.', 'is-success');
        $this->redirect('/profile');
    }

    public function changePassword(): void
    {
        $this->requireAuth();
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect('/profile');
        }
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['new_password_confirm'] ?? '';
        $user = $this->user();

        if (!password_verify($current, $user['password_hash'])) {
            $this->flash('Current password is incorrect.', 'is-danger');
            $this->redirect('/profile');
        }
        $errs = \App\Support\Validator::validate(['p' => $new], ['p' => 'required|min:8']);
        if ($new !== $confirm) {
            $errs['p'][] = 'Password confirmation does not match.';
        }
        if (!empty($errs)) {
            foreach (\App\Support\Validator::flattenErrors($errs) as $m) {
                $this->flash($m, 'is-warning');
            }
            $this->redirect('/profile');
        }
        \App\Models\User::update((int)$user['id'], ['password_hash' => password_hash($new, PASSWORD_DEFAULT)]);
        $this->flash('Password changed.', 'is-success');
        $this->redirect('/profile');
    }
}

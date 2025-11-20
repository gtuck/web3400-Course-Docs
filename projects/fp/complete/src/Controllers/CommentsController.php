<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Post;
use App\Models\Comment;

class CommentsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    public function store(string $slug): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid CSRF token.', 'is-danger');
            $this->redirect('/posts/' . $slug);
        }

        $post = Post::findBySlug($slug);
        if (!$post || ($post['status'] ?? '') !== 'published') {
            $this->flash('Post not available.', 'is-warning');
            $this->redirect('/');
        }

        $body = trim($_POST['body'] ?? '');

        $errors = [];
        if ($body === '') {
            $errors['body'] = 'Comment body is required.';
        } elseif (mb_strlen($body) > 1000) {
            $errors['body'] = 'Comment must be 1000 characters or fewer.';
        }

        if ($errors) {
            $this->flash(reset($errors), 'is-danger');
            $this->redirect('/posts/' . $slug);
        }

        $user = $this->user();
        $userId = (int)($user['id'] ?? 0);

        Comment::create([
            'post_id' => (int)$post['id'],
            'user_id' => $userId,
            'body' => $body,
            'status' => 'pending',
        ]);

        $this->flash('Comment submitted for review.', 'is-success');
        $this->redirect('/posts/' . $slug);
    }

    public function destroy(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid CSRF token.', 'is-danger');
            $this->redirect('/');
        }

        $comment = Comment::find($id);
        if (!$comment) {
            $this->redirect('/');
        }

        $user = $this->user();
        $role = $user['role'] ?? 'user';
        $userId = (int)($user['id'] ?? 0);

        $post = Post::find((int)$comment['post_id']);
        $slug = $post['slug'] ?? '';

        if ((int)$comment['user_id'] !== $userId && !in_array($role, ['admin', 'editor'], true)) {
            $this->flash('You are not allowed to delete this comment.', 'is-danger');
            $this->redirect('/posts/' . $slug);
        }

        // Soft delete; only decrement counter when this comment was previously counted
        if ($comment['status'] !== 'deleted') {
            Comment::update($id, ['status' => 'deleted']);
            if ($comment['status'] === 'published') {
                Post::decrementComments((int)$comment['post_id']);
            }
        }

        $this->flash('Comment deleted.', 'is-success');
        $this->redirect('/posts/' . $slug);
    }
}

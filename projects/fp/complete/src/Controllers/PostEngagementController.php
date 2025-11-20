<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\PostFavorite;

class PostEngagementController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    public function like(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid CSRF token.', 'is-danger');
            $this->redirect('/'); // fallback
        }

        $post = Post::find($id);
        if (!$post || ($post['status'] ?? '') !== 'published') {
            $this->flash('Post not available.', 'is-warning');
            $this->redirect('/');
        }

        $user = $this->user();
        $userId = (int)($user['id'] ?? 0);

        if (!PostLike::existsForUser($id, $userId)) {
            PostLike::create([
                'post_id' => $id,
                'user_id' => $userId,
            ]);
            Post::incrementLikes($id);
        }

        $this->redirect('/posts/' . $post['slug']);
    }

    public function unlike(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid CSRF token.', 'is-danger');
            $this->redirect('/');
        }

        $post = Post::find($id);
        if (!$post) {
            $this->redirect('/');
        }

        $user = $this->user();
        $userId = (int)($user['id'] ?? 0);

        if (PostLike::deleteForUser($id, $userId)) {
            Post::decrementLikes($id);
        }

        $this->redirect('/posts/' . $post['slug']);
    }

    public function fav(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid CSRF token.', 'is-danger');
            $this->redirect('/');
        }

        $post = Post::find($id);
        if (!$post || ($post['status'] ?? '') !== 'published') {
            $this->flash('Post not available.', 'is-warning');
            $this->redirect('/');
        }

        $user = $this->user();
        $userId = (int)($user['id'] ?? 0);

        if (!PostFavorite::existsForUser($id, $userId)) {
            PostFavorite::create([
                'post_id' => $id,
                'user_id' => $userId,
            ]);
            Post::incrementFavs($id);
        }

        $this->redirect('/posts/' . $post['slug']);
    }

    public function unfav(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid CSRF token.', 'is-danger');
            $this->redirect('/');
        }

        $post = Post::find($id);
        if (!$post) {
            $this->redirect('/');
        }

        $user = $this->user();
        $userId = (int)($user['id'] ?? 0);

        if (PostFavorite::deleteForUser($id, $userId)) {
            Post::decrementFavs($id);
        }

        $this->redirect('/posts/' . $post['slug']);
    }
}

<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\PostFavorite;
use App\Support\Database;

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

        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT 1 FROM `post_likes` WHERE `post_id` = :post_id AND `user_id` = :user_id LIMIT 1');
        $stmt->bindValue(':post_id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $exists = (bool)$stmt->fetchColumn();

        if (!$exists) {
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

        $pdo = Database::pdo();
        $stmt = $pdo->prepare('DELETE FROM `post_likes` WHERE `post_id` = :post_id AND `user_id` = :user_id');
        $stmt->bindValue(':post_id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $deleted = $stmt->execute();
        if ($deleted && $stmt->rowCount() > 0) {
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

        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT 1 FROM `post_favorites` WHERE `post_id` = :post_id AND `user_id` = :user_id LIMIT 1');
        $stmt->bindValue(':post_id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $exists = (bool)$stmt->fetchColumn();

        if (!$exists) {
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

        $pdo = Database::pdo();
        $stmt = $pdo->prepare('DELETE FROM `post_favorites` WHERE `post_id` = :post_id AND `user_id` = :user_id');
        $stmt->bindValue(':post_id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $deleted = $stmt->execute();
        if ($deleted && $stmt->rowCount() > 0) {
            Post::decrementFavs($id);
        }

        $this->redirect('/posts/' . $post['slug']);
    }
}

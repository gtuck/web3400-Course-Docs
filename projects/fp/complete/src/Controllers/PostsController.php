<?php
namespace App\Controllers;

use App\Controller;
use App\Models\Post;
use App\Support\Database;
use App\Support\Time;

class PostsController extends Controller
{
    public function show(string $slug): void
    {
        $user = $this->user();
        $userId = $user ? (int)$user['id'] : null;

        $post = Post::findBySlugWithAuthorAndEngagement($slug, $userId);
        if (!$post || $post['status'] !== 'published') {
            http_response_code(404);
            echo 'Post not found';
            return;
        }

        // Load published comments for this post
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("
            SELECT c.*, u.name AS user_name
            FROM `comments` c
            JOIN `users` u ON u.id = c.user_id
            WHERE c.post_id = :post_id AND c.status = 'published'
            ORDER BY c.created_at ASC
        ");
        $stmt->bindValue(':post_id', (int)$post['id'], \PDO::PARAM_INT);
        $stmt->execute();
        $comments = $stmt->fetchAll();

        foreach ($comments as &$c) {
            $c['created_human'] = Time::ago($c['created_at']);
        }

        $this->render('posts/show', [
            'title' => $post['title'],
            'post' => $post,
            'published_human' => Time::ago($post['published_at']),
            'comments' => $comments,
        ]);
    }
}

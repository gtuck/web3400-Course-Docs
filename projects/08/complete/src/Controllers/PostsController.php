<?php
namespace App\Controllers;

use App\Controller;
use App\Models\Post;
use App\Models\Comment;
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
        $comments = Comment::publishedForPost((int)$post['id']);

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

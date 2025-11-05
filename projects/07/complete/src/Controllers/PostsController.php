<?php
namespace App\Controllers;

use App\Controller;
use App\Models\Post;
use App\Support\Time;

class PostsController extends Controller
{
    public function show(string $slug): void
    {
        $post = Post::findBySlugWithAuthor($slug);
        if (!$post || $post['status'] !== 'published') {
            http_response_code(404);
            echo 'Post not found';
            return;
        }
        $this->render('posts/show', [
            'title' => $post['title'],
            'post' => $post,
            'published_human' => Time::ago($post['published_at']),
        ]);
    }
}


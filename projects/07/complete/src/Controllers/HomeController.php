<?php
// filepath: projects/05/src/Controllers/HomeController.php
namespace App\Controllers;

use App\Controller;
use App\Models\Post;
use App\Support\Time;

class HomeController extends Controller
{
    public function index(): void
    {
        // 10 most recent, featured, published posts
        $posts = Post::recentFeaturedWithAuthors(10);

        // Map with human time strings
        foreach ($posts as &$p) {
            $p['published_human'] = Time::ago($p['published_at']);
        }

        $this->render('index', [
            'title' => 'Home',
            'posts' => $posts,
        ]);
    }
}

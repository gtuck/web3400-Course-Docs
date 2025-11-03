<?php
// filepath: projects/05/src/Controllers/HomeController.php
namespace App\Controllers;

use App\Controller;
use App\Models\Post;

class HomeController extends Controller
{
    public function index(): void
    {
        // Fetch blog posts using static method (BaseModel pattern)
        $posts = Post::all(orderBy: 'created_at DESC');

        $this->render('index', [
            'title' => 'Home',
            'posts' => $posts,
        ]);
    }
}

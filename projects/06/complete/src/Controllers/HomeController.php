<?php
/**
 * Home Controller
 *
 * Handles the application's home page, displaying a list of blog posts.
 */

namespace App\Controllers;

use App\Controller;
use App\Models\Blog;

class HomeController extends Controller
{
    /**
     * Display the home page with blog posts
     *
     * Fetches all blog posts ordered by creation date (newest first)
     * and renders them on the home page.
     *
     * Route: GET /
     */
    public function index(): void
    {
        $posts = Blog::all(orderBy: 'created_at DESC');

        $this->render('index', [
            'title' => 'Home',
            'posts' => $posts
        ]);
    }
}

<?php
/**
 * HomeController
 *
 * PURPOSE:
 * Handles requests for the home page/index route
 *
 * RESPONSIBILITIES:
 * - Fetch blog posts from the database
 * - Render the home page view with posts data
 *
 * USAGE:
 * Accessed via route: GET /
 */

namespace App\Controllers;

use App\Controller;
use App\Models\Blog;

class HomeController extends Controller
{
    /**
     * Display the home page with blog posts
     *
     * PROCESS:
     * 1. Fetch all blog posts ordered by creation date (newest first)
     * 2. Pass posts to the index view for display
     *
     * ROUTE: GET /
     *
     * @return void
     */
    public function index()
    {
        // Fetch all blog posts, ordered by creation date (newest first)
        $posts = Blog::all(orderBy: 'created_at DESC');

        // Render the home page view with posts data
        $this->render('index', [
            'title' => 'Home',
            'posts' => $posts
        ]);
    }
}

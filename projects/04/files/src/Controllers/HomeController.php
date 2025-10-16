<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Blog;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch posts via BaseModel-powered Blog model
        $posts = Blog::all(limit: 100);
        $this->render('index', ['posts' => $posts]);
    }
}

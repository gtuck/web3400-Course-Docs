<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Blog;

class HomeController extends Controller
{
    public function index()
    {
        $blog = new Blog();
        $posts = $blog->getPosts();
        $this->render('index', ['posts' => $posts]);
    }
}


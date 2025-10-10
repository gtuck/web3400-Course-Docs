<?php

class Posts
{
    public function index()
    {
        require "src/models/post.php";

        $model = new Post;

        $posts = $model->getData();

        require "views/posts_index.php";
    }
}

<?php

class Home
{
    public function index()
    {
        require 'src/models/post.php';

        $model = new Post;

        $posts = $model->getData();

        //require 'view.php';   // TODO changed
        require 'views/home_index.php';
    }

}

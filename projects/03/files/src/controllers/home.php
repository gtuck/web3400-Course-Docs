<?php

class Home
{
    public function index()
    {
        require 'src/models/article.php';

        $model = new Article;

        $articles = $model->getData();

        //require 'view.php';   // TODO changed
        require 'views/home_index.php';
    }

}

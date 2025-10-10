<?php

class Post
{
    public function getData()
    {
        $conn = mysqli_connect('localhost', 'root', '', 'mvc');

        $result = mysqli_query($conn, 'SELECT * FROM posts');

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

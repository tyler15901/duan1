<?php

class HomeController
{
    public function index() 
    {
        $title = 'Home';
        $view = 'frontend/index';
        require_once PATH_VIEW . 'main.php';
    }
}
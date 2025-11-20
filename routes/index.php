<?php

$action = $_GET['action'] ?? '/';

match ($action) {
    '/'                     => (new HomeController)->index(),
    'home'                  => (new App\Controllers\FrontendController)->index(),
    'tours'                => (new App\Controllers\FrontendController)->list(),
    'tour'                 => (new App\Controllers\FrontendController)->detail(),
    default                 => (new HomeController)->index(),
};
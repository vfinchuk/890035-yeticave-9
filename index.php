<?php

/* Config file */
include_once(__DIR__ . '/bootstrap.php');


$content = include_template('index.php', [
    'categories' => $categories,
    'lots'       => $lots,
]);

$layout = include_template('layout.php', [
    'title'      => $title,
    'categories' => $categories,
    'content'    => $content,
    'is_auth'    => $isAuth,
    'user_name'  => $userName
]);

print $layout;
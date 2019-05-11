<?php
include_once(__DIR__ . '/bootstrap.php');

$title = 'Yeticave - 404 Страница не найдена';

$categories = get_categories($connection);

$content = include_template('404.php', [
    'categories' => $categories,
]);

$layout = include_template('layout.php', [
    'title'      => $title,
    'categories' => $categories,
    'content'    => $content,
    'user'    => $user,

]);

print $layout;

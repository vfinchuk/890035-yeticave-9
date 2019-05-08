<?php
/* Config file */
include_once(__DIR__ . '/bootstrap.php');

$title = 'Yeticave - форма регистрации нового пользователя';

$categories = get_categories($connection);


$content = include_template('login.php', [
    'categories' => $categories
]);


$layout = include_template('layout.php', [
    'title'      => $title,
    'categories' => $categories,
    'content'    => $content,
    'is_auth'    => $isAuth,
    'user_name'  => $userName,

]);


print $layout;
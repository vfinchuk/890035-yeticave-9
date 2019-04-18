<?php
require_once(__DIR__ . '/helpers.php');
require_once(__DIR__ . '/includes/data.php');


$content = include_template('index.php', [
    'data_categories' => $categories,
    'dat_lots'        => $lots,
]);

$layout = include_template('layout.php', [
    'data_title'      => $title,
    'data_categories' => $categories,
    'content'         => $content,
    'is_auth'         => $isAuth,
    'user_name'       => $userName
]);

print $layout;
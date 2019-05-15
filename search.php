<?php
include_once(__DIR__ . '/bootstrap.php');

$title = 'Yeticave - страница поиска';

$categories = get_categories($connection);
$lots = null;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $search = $_GET['search'] ?? '';

    if ($search) {
        $lots = get_search_lots($connection, $search);
    }
}

$content = include_template('search.php', [
    'categories' => $categories,
    'lots'       => $lots
]);

$layout = include_template('layout.php', [
    'title'      => $title,
    'categories' => $categories,
    'content'    => $content,
    'user'       => $user,
]);

print $layout;
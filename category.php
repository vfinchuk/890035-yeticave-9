<?php
/* Config file */
include_once(__DIR__ . '/bootstrap.php');

$category_id = $_GET['id'] ?? null;
$categories = get_categories($connection);
$category = get_category($connection, $category_id);

if ($category_id && $category) {

    $title = "Категория - {$category['name']}";

    $lots = get_lots_by_category($connection, $category_id);

    $content = include_template('category.php', [
        'categories' => $categories,
        'lots'        => $lots,
        'current_category' => $category
    ]);

} else {

    $title = 'Категория не найдена.';

    http_response_code(404);
    $content = include_template('404.php', [
        'categories' => $categories
    ]);

}

$layout = include_template('layout.php', [
    'title'      => $title,
    'categories' => $categories,
    'content'    => $content,
    'session'    => $session,

]);

print $layout;
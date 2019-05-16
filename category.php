<?php
/* Config file */
include_once(__DIR__ . '/bootstrap.php');

$category_id = $_GET['id'] ?? null;
$categories = get_categories($connection);
$category = get_category($connection, intval($category_id));
$lots = null;
$pagination = null;
if ($category_id && $category) {

    $title = "Категория - {$category['name']}";
    $lots_count = count_lots_by_category($connection, intval($category_id));
    $lots_per_page = 3;

    if ($lots_count) {
        $pagination = pagination($lots_count, $lots_per_page);

        $lots = get_lots_by_category_per_page(
            $connection,
            intval($category_id),
            $lots_per_page,
            $pagination['offset']
        );

        $pagination['category_id'] = $category_id;
    }


    $content = include_template('category.php', [
        'categories'       => $categories,
        'lots'             => $lots,
        'current_category' => $category,
        'pagination'       => $pagination
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
    'user'       => $user,

]);

print $layout;
<?php
include_once(__DIR__ . '/bootstrap.php');

$title = 'Yeticave - страница поиска';

$categories = get_categories($connection);
$lots = null;
$pagination = null;
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $search = $_GET['search'] ?? '';

    if ($search) {
        $search = filter_search_query($search);
        $lots_count = count_lots_by_search($connection, $search);
        $lots_per_page = 3;

        if ($lots_count) {
            $pagination = pagination($lots_count, $lots_per_page);

            $lots = get_search_lots_by_page(
                $connection,
                $search,
                $lots_per_page,
                $pagination['offset']
            );

            $pagination['search'] = $search;
        }
    }
}

$content = include_template('search.php', [
    'categories' => $categories,
    'lots'       => $lots,
    'pagination' => $pagination
]);

$layout = include_template('layout.php', [
    'title'      => $title,
    'categories' => $categories,
    'content'    => $content,
    'user'       => $user,
]);

print $layout;
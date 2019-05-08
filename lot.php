<?php
/* Config file */
include_once(__DIR__ . '/bootstrap.php');

$lot_id = $_GET['id'] ?? null;
$lot = null;
$categories = get_categories($connection);

if ($lot_id) {

    $lot = get_lot($connection, $lot_id);

}

if ($lot_id && $lot) {

    $title = "Лот - {$lot['name']}";

    $content = include_template('lot.php', [
        'categories' => $categories,
        'lot'        => $lot
    ]);

} else {

    $title = 'Лот не найден.';

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
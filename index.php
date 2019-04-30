<?php
/* Config file */
include_once(__DIR__ . '/bootstrap.php');


if (!$yeticave_db) {

    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);

} else {
    /**
     * Вывод категорий из БД
     */
    $sql_category = "SELECT * FROM categories";
    $categories = db_fetch_data($yeticave_db, $sql_category);

    /**
     * Вывод массива лотов из БД
     */
    $sql_lots = "SELECT l.name, start_price, image, c.name AS category_name
                FROM lots l
                JOIN categories c ON l.category_id = c.id
                GROUP BY l.id
                ORDER BY l.create_time DESC";

    $lots = db_fetch_data($yeticave_db, $sql_lots);
}

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
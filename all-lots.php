<?php
/* Config file */
include_once(__DIR__ . '/bootstrap.php');

$catId = $_GET['catId'] ?? null;

if (!$yeticave_db) {

    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);

} else {

    /**
     * Вывод категорий из БД
     */
    $sql_categories = "SELECT * FROM categories";
    $categories = db_fetch_data($yeticave_db, $sql_categories);

    /**
     * Активная категория
     */
    $sql_category = "SELECT * FROM categories WHERE id = ?";
    $category = db_fetch_data($yeticave_db, $sql_category, [$catId], true);

    /**
     * Вывод массива лотов из БД
     */
    $sql_lots = "SELECT l.id, l.name, start_price, content, image, c.name AS category_name
                FROM lots l
                LEFT JOIN categories c ON l.category_id = c.id
                WHERE c.id = ?";

    $lots = db_fetch_data($yeticave_db, $sql_lots, [$catId]);

}


if (!$lots) {
    header('Location: /404.php');
}

$content = include_template('all-lots.php', [
    'categories' => $categories,
    'category' => $category,
    'lots' => $lots,
    'cat_id' => $catId
]);

$layout = include_template('layout.php', [
    'title'      => $title,
    'categories' => $categories,
    'content'    => $content,
    'is_auth'    => $isAuth,
    'user_name'  => $userName,

]);

print $layout;
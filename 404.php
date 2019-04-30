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

}

$content = include_template('404.php', [
    'categories' => $categories,
]);

$layout = include_template('layout.php', [
    'title'      => $title,
    'categories' => $categories,
    'content'    => $content,
    'is_auth'    => $isAuth,
    'user_name'  => $userName,

]);

print $layout;

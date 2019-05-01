<?php
/* Config file */
include_once(__DIR__ . '/bootstrap.php');

$title = 'Yeticave - просмотр лота';

$lotId = $_GET['id'] ?? null;


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
     * Вывод лота по ID из БД
     */
    $sql_lot = "SELECT l.name, start_price, step_rate, content, image, c.name AS category_name
                FROM lots l
                LEFT JOIN categories c ON l.category_id = c.id
                WHERE l.id = ?";

    $lot = db_fetch_data($yeticave_db, $sql_lot, [$lotId], true);

    /**
     * Ставки этого лота
     */
    $sql_bets = "SELECT u.name, b.amount, b.create_time FROM bets b
                 JOIN users u ON b.user_id = u.id
                 WHERE b.lot_id = ?
                 ORDER BY b.create_time DESC
                 LIMIT 10;";

    $bets = db_fetch_data($yeticave_db, $sql_bets, [$lotId]);

}

if (!$lot) {
    header('Location: /404.php');
}


$content = include_template('lot.php', [
    'categories' => $categories,
    'lot'        => $lot,
    'bets'       => $bets
]);

$layout = include_template('layout.php', [
    'title'      => $title,
    'categories' => $categories,
    'content'    => $content,
    'is_auth'    => $isAuth,
    'user_name'  => $userName,

]);


print $layout;
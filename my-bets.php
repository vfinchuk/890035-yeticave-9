<?php
include_once(__DIR__ . '/bootstrap.php');

$title = 'Yeticave - мои ставки';

$categories = get_categories($connection);
$lots = get_lots($connection);

$my_bets = get_user_bets($connection, intval($user['id']));

if (!$user) {
    header('Location: login.php');
    exit();
}

$content = include_template('my-bets.php', [
    'categories' => $categories,
    'my_bets'    => $my_bets,
    'user'       => $user,
]);

$layout = include_template('layout.php', [
    'title'      => $title,
    'categories' => $categories,
    'content'    => $content,
    'user'       => $user,
]);


print $layout;
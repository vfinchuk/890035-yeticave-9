<?php
/* Config file */
include_once(__DIR__ . '/bootstrap.php');

$title = 'Yeticave - форма регистрации нового пользователя';

$categories = get_categories($connection);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $auth_data = $_POST['auth'] ?? null;

    if (!$auth_data) {
        die('Отсутствуют данные аутентификации');
    }

    $errors = validate_auth_form($connection, $auth_data);

    if ($errors) {
        $content = include_template('login.php', [
            'categories' => $categories,
            'errors' => $errors
        ]);
    } else {

//        var_dump($auth_data);
//        die;
//        $content = include_template('login.php', [
//            'categories' => $categories
//        ]);

    }


} else {
    $content = include_template('login.php', [
        'categories' => $categories
    ]);
}


$layout = include_template('layout.php', [
    'title'      => $title,
    'categories' => $categories,
    'content'    => $content,
    'is_auth'    => $isAuth,
    'user_name'  => $userName,

]);

print $layout;
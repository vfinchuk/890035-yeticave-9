<?php
/* Config file */
include_once(__DIR__ . '/bootstrap.php');

$title = 'Yeticave - форма аутентификации';

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
        $user_data = get_user_by_email($connection, $auth_data['email']);


        $_SESSION['user'] = $user_data;

        var_dump($_SESSION['user']);

        header('Location: index.php');
        exit();
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
    'session'    => $session,
]);

print $layout;
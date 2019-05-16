<?php
include_once(__DIR__ . '/bootstrap.php');

$title = 'Yeticave - форма регистрации нового пользователя';
$categories = get_categories($connection);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user_data = $_POST['user'] ?? null;
    $avatar = $_FILES['avatar'] ?? null;

    if (!$user_data) {
        die('Отсутствуют данные пользователя');
    }

    $errors = validate_user_form($connection, $user_data, $avatar);

    if ($errors) {

        $content = include_template('sign-up.php', [
            'categories' => $categories,
            'errors'     => $errors
        ]);

    } else {
        $user_data = filter_form_data($user_data);

        $user_data['password'] = password_hash($user_data['password'],
            PASSWORD_DEFAULT);
        $user_data['avatar'] = upload_file($avatar);
        $user_id = insert_user($connection, $user_data);

        header('Location: index.php');
        exit();
    }

} else {
    $content = include_template('sign-up.php', [
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
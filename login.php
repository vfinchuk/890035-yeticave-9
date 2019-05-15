<?php
include_once(__DIR__ . '/bootstrap.php');

$title = 'Yeticave - форма аутентификации';
$categories = get_categories($connection);

if ($user) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $auth_data = $_POST['auth'] ?? null;
    if (!$auth_data) {
        die('Отсутствуют данные аутентификации');
    }

    $errors = null;

    if ($error = validate_auth_login($auth_data['email'])) {
        $errors['email'] = $error;
    }
    if ($error = validate_auth_password($auth_data['password'])) {
        $errors['password'] = $error;
    }

    $user = get_user_by_email($connection, $auth_data['email']);
    if (!$errors) {
        $auth_data = filter_form_data($auth_data);
        $errors = validate_login($user, $auth_data['password']);
    }

    if ($errors) {
        $content = include_template('login.php', [
            'categories' => $categories,
            'errors'     => $errors
        ]);
    } else {
        $_SESSION['user'] = $user;
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
    'user'       => $user,
]);

print $layout;
<?php
include_once(__DIR__ . '/bootstrap.php');
$title = 'Добавить новый лот';
$categories = get_categories($connection);

if (!$user) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $lot_data = $_POST['lot'] ?? null;
    $lot_image = $_FILES['lot-image'] ?? null;
    $lot_data['user_id'] = $user['id'] ?? null;

    if (!$lot_data || !$lot_image) {
        die('Отсутствуют данные лота в запросе');
    }

    $errors = validate_lot_form($connection, $lot_data, $lot_image);

    if ($errors) {

        $content = include_template('add-lot.php', [
            'categories' => $categories,
            'errors'     => $errors
        ]);

    } else {
        $lot_data = filter_form_data($lot_data);

        $lot_data['lot-image'] = upload_file($lot_image);
        $lot_id = insert_lot($connection, $lot_data);

        header('Location: lot.php?id=' . $lot_id);
    }

} else {

    $content = include_template('add-lot.php', [
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
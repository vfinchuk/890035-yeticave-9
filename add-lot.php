<?php
include_once(__DIR__ . '/bootstrap.php');

$title = 'Добавить новый лот';

$categories = get_categories($connection);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $lot_data = $_POST['lot'] ?? null;
    $lot_image = $_FILES['lot-image'] ?? null;
    if (!$lot_data || !$lot_image) {
        die('Отсутствуют данные лота в запросе');
    }

    $errors = validate_lot_form($lot_data, $lot_image);

    if ($errors) {
        $content = include_template('add-lot.php', [
            'categories' => $categories,
            'errors'     => $errors
        ]);
    } else {
        filter_form_data($lot_data);

        $lot_data_image = validate_lot_image($_FILES['lot-image'], true, true);
        $lot_id = insert_lot_to_db($connection,
            3,
            $lot_data['category'],
            $lot_data['end-time'],
            $lot_data['name'],
            $lot_data['content'],
            $lot_data_image,
            $lot_data['start-price'],
            $lot_data['step-rate']
        );

        if ($lot_id) {
            $lot = get_lot($connection, $lot_id);

            header('Location: lot.php?id=' . $lot_id);
            $content = include_template('lot.php', [
                'categories' => $categories,
                'lot'        => $lot
            ]);
        }
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
    'is_auth'    => $isAuth,
    'user_name'  => $userName,

]);

print $layout;
<?php

/* Config file */
include_once(__DIR__ . '/bootstrap.php');

$title = 'Добавить новый лот.';

$categories = get_categories($connection);

/**
 * Валидация формы
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lot = $_POST['lot'];

    $errors = [];
    $required = ['name', 'category', 'content', 'start-price', 'step-rate', 'end-time'];

    // Валидация текстовых полей формы
    foreach ($required as $key) {

        if (empty($lot[$key])) {
            $errors[$key] = 'Это поле надо заполнить.';
        } else {

            htmlspecialchars($lot[$key]);

            if (is_category_valid($lot['category']) !== true) {
                $errors['category'] = is_category_valid($lot['category']);
            }

            if (!is_numeric(is_string_number_valid($lot['start-price']))) {
                $errors['start-price'] = is_string_number_valid($lot['start-price']);
            }

            if (!is_numeric(is_string_number_valid($lot['step-rate']))) {
                $errors['step-rate'] = is_string_number_valid($lot['step-rate']);
            }

            if (is_end_time_valid($lot['end-time']) !== true) {
                $errors['end-time'] = is_end_time_valid($lot['end-time']);
            }

        }
    }

    if (is_image_valid($_FILES['lot-image']) !== true) {
        $errors['lot-image'] = is_image_valid($_FILES['lot-image'], $errors);
    } else {
        $lot_image = is_image_valid($_FILES['lot-image'], true, true);
    }


    // Условие на вывод ошибок при валидации
    if (count($errors)) {

        $content = include_template('add-lot.php', [
            'categories' => $categories,
            'errors'     => $errors
        ]);

    } else {
        // Ошибок не найдено, добавляем данные в БД и перенаправляем пользователя на страницу добавленого лота
        $lot_id = insert_lot_to_db($connection,
            3,
            $lot['category'],
            $lot['end-time'],
            $lot['name'],
            $lot['content'],
            $lot_image,
            $lot['start-price'],
            $lot['step-rate']
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
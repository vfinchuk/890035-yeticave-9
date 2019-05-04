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
        }

        if (!is_numeric($lot['category'])) {
            $errors['category'] = 'Это поле надо заполнить.';
        }

        if (!empty($lot['start-price']) && !is_numeric($lot['start-price'])) {
            $errors['start-price'] = 'Это поле надо заполнить.';
            $errors['start-price-not-number'] = 'Только числовое значение';
        }

        if (!empty($lot['step-rate']) && !is_numeric($lot['step-rate'])) {
            $errors['step-rate'] = 'Это поле надо заполнить.';
            $errors['step-rate-not-number'] = 'Только числовое значение';
        }

        if (!empty($lot['end-time']) && (strtotime('now') > strtotime($lot['end-time']))) {
            $errors['end-time'] = 'Это поле надо заполнить.';
            $errors['end-time-no-future'] = 'Нужно указать дату из будущего';
        }
    }

    // Валидация файла-изображения
    if (!empty($_FILES['lot-image']['name'])) {

        $image = $_FILES['lot-image'];

        $tmp_name = $image['tmp_name'];
        $path = $image['name'];

        $file_name = uniqid() . '.jpeg';
        $image['path'] = $file_name;
        $file_path = __DIR__ . DIRECTORY_SEPARATOR . 'uploads/';

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);


        if ($file_type === "image/png" || $file_type === "image/jpeg") {
            if (!count($errors)) {
                move_uploaded_file($tmp_name, $file_path . $file_name);
            }
        } else {
            $errors['lot-image'] = 'Загрузите картинку в формате PNG или JPEG';
        }

    } else {
        $errors['lot-image'] = 'Вы не загрузили файл';
    }


    if (count($errors)) {
        // Вывод ошибок при валидации
        $content = include_template('add-lot.php', [
            'categories' => $categories,
            'errors'     => $errors
        ]);

    } else {
        // Все поля заполненвы верно, добавляем данные в БД и перенаправляем пользователя на добавленый лот

        $content = include_template('lot.php', [
            'categories' => $categories,
//            'lot'        => $lot
        ]);

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
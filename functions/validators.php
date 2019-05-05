<?php
/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date): bool
{
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Проверяет дату окончания лота
 *
 * @param string $end_time Дата в виде строки
 *
 * @return string вернет true или текст ошибки в случае не валидности
 */
function is_end_time_valid(string $end_time)
{
    if (empty($end_time)) {
        $error = 'Введите дату завершения торгов';
    } elseif (is_date_valid($end_time)) {

        $ts_end_time = strtotime($end_time);
        $ts_nex_day = strtotime('+1 day') - time();

        if ($ts_end_time <= time()) {
            $error = 'Нельзя указывать дату из прошлого';
        } elseif ($ts_nex_day > ($ts_end_time - time())) {
            $error = 'Минимальный срок лота 1 день';
        } else {
            return true;
        }

    } else {
        $error = 'Неверный формат даты';
    }

    return $error;
}

/**
 * Проверяет строку на числовое значение
 *
 * @param string $number число или строка
 *
 * @return string в случае ошибки возвращает текст ошибки. В случае успешной валидации целое число
 */
function is_string_number_valid(string $number)
{
    $number = trim($number);
    if (empty($number) && $number !== '0') {
        $error = 'Это поле надо заполнить';
    } elseif (is_numeric($number)) {

        if (intval($number) <= 0) {
            $error = 'Содержимое поля должно быть целым числом больше ноля';
        } else {
            return intval($number);
        }

    } else {
        $error = 'Поле должно содержать только числовое значение';
    }

    return $error;
}

/**
 * Проверяет поле select категории на наличие ID
 *
 * @param string $category
 *
 * @return string Вернет true или строку с текслом ошибки.
 */
function is_category_valid(string $category)
{
    if (!is_numeric($category)) {
        $error = 'Выберите категорию';
    } else {
        return true;
    }

    return $error;
}

/**
 * Проверяет изображение лота на наявность и верный формат
 *
 * @param array $image массив с данными изображения
 * @param bool $save_image флаг на сохранения изображения
 * @param bool $link флаг на возвращение ссылки на изображение из функции
 *
 * @return string Возвращает булевое значение если все условия true. Может вернуть ссылку на изображение если $save_image = true, или возвращает сообщение ошибки если не пройдена валидация.
 */
function is_lot_image_valid(array $image, $save_image = false, $link = false)
{

    $tmp_name = $image['tmp_name'];
    $path = $image['name'];

    if (empty($path)) {
        return $error = 'Нужно выбрать изображение для лота';
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($finfo, $tmp_name);

    if ($file_type === 'image/jpeg' || $file_type === 'image/png') {

        $file_type = str_replace('/', '', strstr($file_type, '/'));
        $file_name = uniqid() . '.' . $file_type;

        $file_path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'uploads/';
        $file_link = DIRECTORY_SEPARATOR . 'uploads/' . $file_name;

        if ($save_image){
            move_uploaded_file($tmp_name, $file_path . $file_name);
        }
        if ($link) {
            return $file_link;
        }

        return true;

    } else {
        return $error = 'Неверный формат изображения. Допустимые форматы JPEG и PNG';
    }
}

/**
 * Функция валидации формы добавления лота
 *
 * @param array $lot_data массив с данными из формы для валидации
 *
 * @return array | bool Вернет true или массив с ошибками
 */
function validate_lot_form ($lot_data)
{
    $errors = [];
    $required = ['name', 'category', 'content', 'start-price', 'step-rate', 'end-time'];
    foreach ($required as $key) {
        if (empty($lot_data[$key])) {
            $errors[$key] = 'Это поле надо заполнить.';
        } else {
            htmlspecialchars($lot_data[$key]);
            if (is_category_valid($lot_data['category']) !== true) {
                $errors['category'] = is_category_valid($lot_data['category']);
            }
            if (!is_numeric(is_string_number_valid($lot_data['start-price']))) {
                $errors['start-price'] = is_string_number_valid($lot_data['start-price']);
            }
            if (!is_numeric(is_string_number_valid($lot_data['step-rate']))) {
                $errors['step-rate'] = is_string_number_valid($lot_data['step-rate']);
            }
            if (is_end_time_valid($lot_data['end-time']) !== true) {
                $errors['end-time'] = is_end_time_valid($lot_data['end-time']);
            }
        }
    }

    if (is_lot_image_valid($_FILES['lot-image']) !== true) {
        $errors['lot-image'] = is_lot_image_valid($_FILES['lot-image'], $errors);
    }

    if(count($errors)) {
        return $errors;
    }

    return true;
}
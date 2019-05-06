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
 * Проверяет имя лота
 *
 * @param string $name Имя лота
 *
 * @return string вернет null или текст ошибки
 */
function validate_lot_name($name)
{
    if (empty($name)) {
        return 'Необходимо ввести имя лота';
    }

    if (mb_strlen($name) > 128) {
        return 'Имя лота не может превышать 128 символов';
    }

    return null;
}

/**
 * Проверяет поле select категории на наличие ID
 *
 * @param int $category
 *
 * @return string Вернет null или строку с текстом ошибки.
 */
function validate_lot_category($category)
{
    if (!is_numeric($category)) {
        $error = 'Выберите категорию';
    } else {
        return null;
    }

    return $error;
}

/**
 * Проверяет поле для описани лота
 *
 * @param string $name Имя лота
 *
 * @return string вернет null или текст ошибки
 */
function validate_lot_content($content)
{
    if (empty($content)) {
        return 'Необходимо ввести описание для лота';
    }

    if (mb_strlen($content) > 1000) {
        return 'Описание лота не может превышать 1000 символов';
    }

    return null;
}

/**
 * Проверяет изображение лота на наявность и верный формат
 *
 * @param array $image массив с данными изображения
 * @param bool $save_image флаг на сохранения изображения
 * @param bool $link флаг на возвращение ссылки на изображение из функции
 *
 * @return string | null Возвращает null или строку с ошибкой. Возвращает ссылку на изображение если $save_image = true.
 */
function validate_lot_image(array $image, $save_image = false, $link = false)
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

        if ($save_image) {
            move_uploaded_file($tmp_name, $file_path . $file_name);
        }
        if ($link) {
            return $file_link;
        }

        return null;

    } else {
        return $error = 'Неверный формат изображения. Допустимые форматы JPEG и PNG';
    }
}

/**
 * Проверяет стартовую цену лота
 *
 * @param integer $start_price число или строка
 *
 * @return string Возвращает null или текст ошибки
 */
function validate_lot_start_price($start_price)
{
    $start_price = trim($start_price);
    if (empty($start_price) && $start_price !== '0') {
        $error = 'Это поле надо заполнить';
    } elseif (is_numeric($start_price)) {

        if (intval($start_price) <= 0) {
            $error = 'Содержимое поля должно быть целым числом больше ноля';
        } else {
            return null;
        }

    } else {
        $error = 'Поле должно содержать только числовое значение';
    }

    return $error;
}

/**
 * Проверяет шаг ставки лота
 *
 * @param integer $start_price число или строка
 *
 * @return string Возвращает null или текст ошибки
 */
function validate_lot_step_rate($step_rate)
{
    return validate_lot_start_price($step_rate);
}

/**
 * Проверяет дату окончания лота
 *
 * @param string $end_time Дата в виде строки
 *
 * @return string вернет null или текст ошибки
 */
function validate_lot_end_time($end_time)
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
            return null;
        }

    } else {
        $error = 'Неверный формат даты';
    }

    return $error;
}

/**
 * Функция валидации формы добавления лота
 *
 * @param array $lot_data массив с данными из формы для валидации
 *
 * @return array | bool Вернет null или массив с ошибками
 */
function validate_lot_form($lot_data, $lot_image_data)
{
    $errors = [];

    if ($error = validate_lot_name($lot_data['name'])) {
        $errors['name'] = $error;
    }

    if ($error = validate_lot_category($lot_data['category'])) {
        $errors['category'] = $error;
    }

    if ($error = validate_lot_content($lot_data['content'])) {
        $errors['content'] = $error;
    }

    if ($error = validate_lot_image($lot_image_data)) {
        $errors['lot-image'] = $error;
    }

    if ($error = validate_lot_start_price($lot_data['start-price'])) {
        $errors['start-price'] = $error;
    }

    if ($error = validate_lot_step_rate($lot_data['step-rate'])) {
        $errors['step-rate'] = $error;
    }

    if ($error = validate_lot_end_time($lot_data['end-time'])) {
        $errors['end-time'] = $error;
    }

    if (count($errors)) {
        return $errors;
    }

    return null;
}

/**
 * Функция фильтрации данных из формы
 *
 * @param array $form_data массив с данными из формы
 *
 */
function filter_form_data($form_data)
{
    $filter = '';
    foreach ($form_data as $form_item) {
        if(!empty($form_item)) {
            $filter = htmlspecialchars($form_item);
        }
    }

    return $filter;
}
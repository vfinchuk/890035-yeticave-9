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
 * @param       $name string имя лота
 *
 * @return string вернет null или текст ошибки
 */
function validate_lot_name(string $name): ?string
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
 * Проверяет <select> категории на наличие ID
 *
 * @param       $connection array подключение к базе
 * @param       $category int идентификатор категории
 *
 * @return string Вернет null или строку с текстом ошибки.
 */
function validate_lot_category(mysqli $connection, int $category): ?string
{
    if (!is_numeric($category)) {
        return 'Выберите категорию';
    }
    if (!get_category($connection, $category)) {
        return 'Категории с таким идентификатором нет';
    }

    return null;
}

/**
 * Проверяет поле описания лота
 *
 * @param       $content string контентнт лота
 *
 * @return string вернет null или текст ошибки
 */
function validate_lot_content(string $content): ?string
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
 * Проверяет изображение лота
 *
 * @param       $image array массив с данными изображения
 *
 * @return string Возвращает null или строку с ошибкой
 */
function validate_lot_image(array $image): ?string
{
    $tmp_name = $image['tmp_name'];
    $path = $image['name'];

    if (empty($path)) {
        return $error = 'Нужно выбрать изображение для лота';
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($finfo, $tmp_name);

    if ($file_type === 'image/jpeg' || $file_type === 'image/png') {
        return null;
    } else {
        return $error = 'Неверный формат изображения. Допустимые форматы JPEG и PNG';
    }
}

/**
 * Проверяет стартовую цену лота
 *
 * @param       $start_price integer число или строка
 *
 * @return string Возвращает null или текст ошибки
 */
function validate_lot_start_price(string $start_price): ?string
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
 * @param       $start_price integer число или строка
 *
 * @return string Возвращает null или текст ошибки
 */
function validate_lot_step_rate(string $step_rate): ?string
{
    return validate_lot_start_price($step_rate);
}

/**
 * Проверяет дату окончания лота
 *
 * @param       $end_time string дата в виде строки
 *
 * @return string вернет null или текст ошибки
 */
function validate_lot_end_time(string $end_time): ?string
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
 * @param       $connection mysqli Ресурс соединения
 * @param       $lot_data array массив с данными из формы
 * @param       $lot_image array массив с данными изображения лота
 *
 * @return array | bool Вернет null или массив с ошибками
 */
function validate_lot_form(mysqli $connection, array $lot_data, array $lot_image): ?array
{
    $errors = [];

    if ($error = validate_lot_name($lot_data['name'])) {
        $errors['name'] = $error;
    }
    if ($error = validate_lot_category($connection, intval($lot_data['category']))) {
        $errors['category'] = $error;
    }
    if ($error = validate_lot_content($lot_data['content'])) {
        $errors['content'] = $error;
    }
    if ($error = validate_lot_image($lot_image)) {
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
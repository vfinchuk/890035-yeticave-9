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
 * @param array $connection подключение к базе
 * @param int $category идентификатор категории
 *
 * @return string Вернет null или строку с текстом ошибки.
 */
function validate_lot_category($connection, $category)
{
    if (!is_numeric($category)) {
        return 'Выберите категорию';
    }
    if (!get_category_by_id($connection, $category)) {
        return 'Категории с таким идентификатором нет';
    }

    return null;
}

/**
 * Проверяет поле для описани лота
 *
 * @param string $content контентнт лота
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
 * Проверяет изображение лота
 *
 * @param array $image массив с данными изображения
 *
 * @return string Возвращает null или строку с ошибкой
 */
function validate_lot_image($image)
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
function validate_lot_form($connection, $lot_data, $lot_image)
{
    $errors = [];

    if ($error = validate_lot_name($lot_data['name'])) {
        $errors['name'] = $error;
    }

    if ($error = validate_lot_category($connection, $lot_data['category'])) {
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

/**
 * Функция фильтрации данных из формы
 *
 * @param array $form_data массив с данными из формы
 *
 * @param return array
 */
function filter_form_data($form_data)
{
    $filter = [];
    foreach ($form_data as $form_key => $form_item) {
        if (!empty($form_item)) {
            $filter[$form_key] = htmlspecialchars($form_item);
        }
    }
    return $filter;
}

/**
 * Проверяет Email пользователя
 *
 * @param string $email
 *
 * @return string вернет null или текст ошибки
 */
function validate_user_email($connection, $email)
{
    if (empty($email)) {
        return 'Введите Email';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Не коректно введен Email';
    }

    if (get_user_by_email($connection, $email)) {
        return 'Пользователь с таким Email уже существует';
    }

    return null;
}

/**
 * Проверяет пароль пользователя
 *
 * @param string $password
 *
 * @return string вернет null или текст ошибки
 */
function validate_user_password($password)
{
    if (empty($password)) {
        return 'Введите пароль';
    }
    if (mb_strlen($password) < 8) {
        return 'Минимальная длина пароля 8 символов';
    }
    if (mb_strlen($password) >= 64) {
        return 'Максимальная длина пароля 64 символа';
    }

    return null;
}

/**
 * Проверяет имя пользователя
 *
 * @param string $name
 *
 * @return string вернет null или текст ошибки
 */
function validate_user_name($name)
{
    if (empty($name)) {
        return 'Введите Ваше имя';
    }
    if (mb_strlen($name) > 128) {
        return 'Имя не может превышать 128 символов';
    }

    return null;
}

/**
 * Проверяет аватар пользователя
 *
 * @param array $avatar
 *
 * @return string вернет null или текст ошибки
 */
function validate_avatar($avatar)
{
    $tmp_name = $avatar['tmp_name'];

    if (!empty($tmp_name)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);

        if ($file_type === 'image/jpeg' || $file_type === 'image/png') {
            return null;
        } else {
            return $error = 'Неверный формат изображения. Допустимые форматы JPEG и PNG';
        }
    }

    return null;
}

/**
 * Проверяет контакты пользователя
 *
 * @param string $contact
 *
 * @return string вернет null или текст ошибки
 */
function validate_user_contact($contact)
{
    if (empty($contact)) {
        return 'Необходимо ввести контактные даннные';
    }
    if (mb_strlen($contact) > 1000) {
        return 'Максимальная длина 1000 символов';
    }

    return null;
}

/**
 * Функция валидации формы нового пользователя
 *
 * @param array $user_data массив с данными нового пользовтаеля
 *
 * @return array | bool Вернет null или массив с ошибками
 */
function validate_user_form($connection, $user_data, $avatar)
{
    $errors = [];

    if ($error = validate_user_email($connection, $user_data['email'])) {
        $errors['email'] = $error;
    }

    if ($error = validate_user_password($user_data['password'])) {
        $errors['password'] = $error;
    }

    if ($error = validate_user_name($user_data['name'])) {
        $errors['name'] = $error;
    }

    if ($error = validate_user_contact($user_data['contact'])) {
        $errors['contact'] = $error;
    }

    if ($error = validate_avatar($avatar)) {
        $errors['avatar'] = $error;
    }

    if (count($errors)) {
        return $errors;
    }

    return null;
}

/**
 * Проверяет логин пользователя
 *
 * @param array $login имейл пользователя
 *
 * @return string вернет null или текст ошибки
 */
function validate_auth_login($connection, $login)
{
    if (empty($login)) {
        return 'Введите Email';
    }
    if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
        return 'Не коректно введен Email';
    }
    if (!get_user_by_email($connection, $login)) {
        return 'Нет пользователя с таким Email';
    }

    return null;
}

/**
 * Проверяет логин пользователя
 *
 * @param array $connection подключение к базе
 * @param string $login логин
 * @param string $password пароль
 *
 * @return string вернет null или текст ошибки
 */
function validate_auth_password($connection, $login, $password)
{
    if (empty($password)) {
        return 'Введите пароль';
    } else {
        if (mb_strlen($password) < 8) {
            return 'Минимальная длина пароля 8символов';
        } else {
            $password_hash = get_password_by_email($connection, $login);
            if (!password_verify($password, $password_hash['password'])) {
                return 'Неверный пароль';
            }
        }
    }

    return null;
}

/**
 * Функция валидации формы авторизации
 *
 * @param array $connection подключение к базе
 * @param array $auth_data массив данных авторизации пользователя
 *
 * @return array вернет null или массив ошибок
 */
function validate_auth_form($connection, $auth_data)
{
    $errors = [];

    if (validate_auth_login($connection, $auth_data['email'])) {
        $errors['email'] = validate_auth_login($connection, $auth_data['email']);
    } elseif (
        validate_auth_login($connection, $auth_data['email']) ||
        validate_auth_password($connection, $auth_data['email'], $auth_data['password'])
    ) {
        $errors['email'] = validate_auth_login($connection, $auth_data['email']);
        $errors['password'] = validate_auth_password($connection, $auth_data['email'], $auth_data['password']);
    }

    if (count($errors)) {
        return $errors;
    }

    return null;
}

/**
 * Функция валидации формы новой ставки
 *
 * @param array $connection подключение к базе
 * @param array $bet_amount массив данных о ставке
 *
 * @return array вернет null или массив ошибок
 */
function validate_bet_form($connection, $bet_amount)
{
    $errors = [];

    if(empty($bet_amount)){
        $errors['bet'] = 'Введите сумму ставки';
    } else {
        if(!is_numeric($bet_amount)){
            $errors['bet'] = 'Только числовое значение';
        }

    }

    if(count($errors)) {
        return $errors;
    }

    return null;
}

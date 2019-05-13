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

/**
 * Функция фильтрации данных из формы
 *
 * @param       $form_data array массив с данными из формы
 *
 * @param return array массив отфильтрованных данных из формы
 */
function filter_form_data(array $form_data): array
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
 * @param       $email string имейл пользователя
 *
 * @return string вернет null или текст ошибки
 */
function validate_user_email(mysqli $connection, string $email): ?string
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
 * @param       $password string пароль пользователя
 *
 * @return string вернет null или текст ошибки
 */
function validate_user_password(string $password): ?string
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
 * @param       $name string имя пользователя
 *
 * @return string вернет null или текст ошибки
 */
function validate_user_name(string $name): ?string
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
 * @param       $avatar array массив данных аватара пользователя
 *
 * @return string вернет null или текст ошибки
 */
function validate_avatar(array $avatar): ?string
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
 * @param       $contact string контакты пользователя
 *
 * @return string вернет null или текст ошибки
 */
function validate_user_contact(string $contact): ?string
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
 * @param       $user_data array массив с данными нового пользовтаеля
 *
 * @return array | bool Вернет null или массив с ошибками
 */
function validate_user_form(mysqli $connection, array $user_data, array $avatar): ?array
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
 * @param       $login array имейл пользователя
 *
 * @return string вернет null или текст ошибки
 */
function validate_auth_login(string $login): ?string
{
    if (empty($login)) {
        return 'Введите Email';
    }
    if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
        return 'Не коректно введен Email';
    }

    return null;
}

/**
 * Проверяет пароль пользователя с сохраненным хешем
 *
 * @param       $password string пароль
 *
 * @return string вернет null или текст ошибки
 */
function validate_auth_password(string $password): ?string
{
    if (empty($password)) {
        return 'Введите пароль';
    }
    if (mb_strlen($password) < 8) {
        return 'Минимальная длина пароля 8символов';
    }
    return null;
}

/**
 * Функция валидации формы авторизации
 *
 * @param       $user array массив данных пользователя
 * @param       $password string пароль пользователя
 *
 * @return array вернет null или массив ошибок
 */
function validate_login(?array $user, string $password)
{
    $errors = [];

    if (!$user) {
        $errors['email'] = 'Пользователь с таким email не найден';
    } elseif (!password_verify($password, $user['password'])) {
        $errors['password'] = 'Неверный пароль';
    }

    if (count($errors)) {
        return $errors;
    }

    return null;
}

/**
 * Функция валидации формы новой ставки
 *
 * @param       $lot_data array массив данных лота
 * @param       $amount array размер ставки
 *
 * @return array вернет null или массив ошибок
 */
function validate_bet_form(array $lot_data, string $amount): ?array
{
    $errors = [];

    if (empty($amount)) {
        $errors['bet'] = 'Введите сумму ставки';
    } else {

        if (!is_numeric($amount)) {
            $errors['bet'] = 'Только числовое значение';
        } elseif (($lot_data['price'] + $lot_data['step_rate']) > $amount) {
            $errors['bet'] = 'Минимальная ставка на этот товар ' . ($lot_data['price'] + $lot_data['step_rate']) . ' ';
            $errors['bet'] .= get_noun_plural_form($lot_data['step_rate'], 'рубль', 'рубля', 'рублей');
        }
    }

    if (count($errors)) {
        return $errors;
    }

    return null;
}

<?php
/**
 * Проверяет Email пользователя
 *
 * @param       string $email имейл пользователя
 *
 * @return string|null Текст ошибки
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
 * @param       string $password пароль пользователя
 *
 * @return string|null Текст ошибки
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
 * @param       string $name имя пользователя
 *
 * @return string|null Текст ошибки
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
 * @param       array $avatar массив данных аватара пользователя
 *
 * @return string|null Текст ошибки
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
            return $error
                = 'Неверный формат изображения. Допустимые форматы JPEG и PNG';
        }
    }

    return null;
}

/**
 * Проверяет контакты пользователя
 *
 * @param       string $contact контакты пользователя
 *
 * @return string|null Текст ошибки
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
 * Валидация формы на добавление нового пользователя
 *
 * @param       array $user_data Массив данных нового пользовтаеля
 *
 * @return array|null Массив ошибок
 */
function validate_user_form(
    mysqli $connection,
    array $user_data,
    array $avatar
): ?array
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
 * @param       string $login имейл пользователя
 *
 * @return string|null Текст ошибки
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
 * @param       string $password пароль
 *
 * @return string|null Текст ошибки
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
 * Валидация формы авторизации
 *
 * @param       array  $user     массив данных пользователя
 * @param       string $password пароль пользователя
 *
 * @return array|null Массив ошибок
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
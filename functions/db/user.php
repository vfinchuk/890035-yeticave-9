<?php
/**
 * Функция добавления пользователя в БД
 *
 * @param       $connection mysqli Ресурс соединения
 * @param       $user_data array данные пользователя
 *
 * @return integer идетификатор нового пользователя
 */
function insert_user(mysqli $connection, array $user_data): int
{
    $sql = "INSERT INTO users (email, password, name, contact, avatar) VALUE (?, ?, ?, ?, ?)";
    $add_user = db_insert_data($connection, $sql, [
        'email'    => $user_data['email'],
        'password' => $user_data['password'],
        'name'     => $user_data['name'],
        'contact'  => $user_data['contact'],
        'avatar'   => $user_data['avatar']
    ]);

    return $add_user;
}

/**
 * Функция вывода пользователя по Email
 *
 * @param       $connection mysqli Ресурс соединения
 * @param       $email string имейл
 *
 * @return array массив пользователя
 */
function get_user_by_email(mysqli $connection, string $email): ?array
{
    $sql = "SELECT * FROM users WHERE email = ?;";
    $user = db_fetch_data($connection, $sql, ['email' => $email], true);

    return $user;
}

/**
 * Функция возвращает хеш пароля по имейлу пользователя
 *
 * @param       $connection mysqli Ресурс соединения
 * @param       $email string имейл пользователя
 *
 * @return array массив с хешом пароля
 */
function get_password_by_email(mysqli $connection, string $email): ?array
{
    $sql = "SELECT password FROM users WHERE users.email = ?;";
    $password = db_fetch_data($connection, $sql, ['email' => $email], true);

    return $password;
}
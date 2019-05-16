<?php
/**
 * Добавляет пользователя в БД
 *
 * @param       mysqli $connection Ресурс соединения
 * @param       array  $user_data  Данные пользователя
 *
 * @return integer|null Идетификатор нового пользователя
 */
function insert_user(mysqli $connection, array $user_data): ?int
{
    $sql
        = "INSERT INTO users (email, password, name, contact, avatar) VALUE (?, ?, ?, ?, ?)";

    $add_user = db_insert_data($connection, $sql, [
        'email' => $user_data['email'],
        'password' => $user_data['password'],
        'name' => $user_data['name'],
        'contact' => $user_data['contact'],
        'avatar' => $user_data['avatar']
    ]);

    return $add_user;
}

/**
 * Возвращает пользователя по Email
 *
 * @param       mysqli $connection Ресурс соединения
 * @param       string $email      Email пользователя
 *
 * @return array|null Данные пользователя
 */
function get_user_by_email(mysqli $connection, string $email): ?array
{
    $sql = "SELECT * FROM users WHERE email = ?;";
    $user = db_fetch_data($connection, $sql, ['email' => $email], true);

    return $user;
}

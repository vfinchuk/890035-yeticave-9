<?php
/**
 * Функция вывода категорий
 *
 * @param       $connection mysqli Ресурс соединения
 *
 * @return      array массив категорий
 */
function get_categories(mysqli $connection): ?array
{
    $sql = "SELECT * FROM categories";
    $categories = db_fetch_data($connection, $sql);

    return $categories;
}

/**
 * Функция вывода категории по идетификатору
 *
 * @param       $connection mysqli Ресурс соединения
 * @param       $id int идентификатор категории
 *
 * @return array масив категории
 */
function get_category(mysqli $connection, int $id): ?array
{
    $sql = "SELECT id, name, code FROM categories WHERE id = ?;";
    $category = db_fetch_data($connection, $sql, ['id' => $id], true);

    return $category;
}
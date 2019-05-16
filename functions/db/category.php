<?php
/**
 * Возвращает категории из БД
 *
 * @param       mysqli $connection Ресурс соединения
 *
 * @return array|null Массив категорий
 */
function get_categories(mysqli $connection): ?array
{
    $sql = "SELECT * FROM categories";
    $categories = db_fetch_data($connection, $sql);

    return $categories;
}

/**
 * Возвращает категорию по идетификатору
 *
 * @param       mysqli $connection Ресурс соединения
 * @param       int    $id         Идентификатор категории
 *
 * @return array|null Массив категории
 */
function get_category(mysqli $connection, int $id): ?array
{
    $sql = "SELECT id, name, code FROM categories WHERE id = ?;";
    $category = db_fetch_data($connection, $sql, ['id' => $id], true);

    return $category;
}
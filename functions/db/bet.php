<?php

/**
 * Функция возвращает ставки лота по идентификатору
 *
 * @param       $connection mysqli Ресурс соединения
 * @param       $lot_id integer идентификатор лота
 *
 * @return array массив ставок
 */
function get_bets_by_lot(mysqli $connection, int $lot_id): ?array
{
    $sql = "SELECT u.id AS user_id, u.name AS user_name, b.amount, b.create_time
              FROM bets b 
              LEFT JOIN users u ON u.id = b.user_id
              WHERE b.lot_id = ?
              ORDER BY b.create_time DESC";
    $bets = db_fetch_data($connection, $sql, ['lot_id' => $lot_id]);

    return $bets;
}


/**
 * Функция возвращает массив последних ставок на каждый из лотов
 *
 * @param       $connection mysqli Ресурс соединения
 * @param       $lot_id integer идентификатор лота
 *
 * @return array массив данных последней ставки
 */
function get_user_win_bets(mysqli $connection): ?array
{
    $sql = "SELECT * FROM bets b WHERE create_time = (SELECT MAX(create_time) FROM bets GROUP BY lot_id HAVING lot_id = b.lot_id)";

//    $sql = "SELECT MAX(create_time) AS create_time, MAX(amount) AS amount FROM bets b GROUP BY lot_id";

    $bet = db_fetch_data($connection, $sql);

    return $bet;
}


/**
 * Функция возвращает текущую цену на лот
 *
 * @param       $connection mysqli Ресурс соединения
 * @param       $user_id int идентификатор пользователя
 *
 * @return array массив всех ставок пользователя
 */
function get_user_bets(mysqli $connection, int $user_id): ?array
{
    $sql = "SELECT l.id AS lot_id, l.name AS lot_name, image, end_time, c.name AS category_name, b.amount AS bet_amount, b.create_time
                FROM lots l
                LEFT JOIN categories c ON l.category_id = c.id
                LEFT JOIN bets b ON l.id = b.lot_id
                WHERE b.user_id = ?
                ORDER BY b.create_time DESC";
    $bets = db_fetch_data($connection, $sql, ['user_id' => $user_id]);

    return $bets;
}
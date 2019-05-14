<?php

/**
 * Функция вывода лотов
 *
 * @param       $connection mysqli Ресурс соединения
 *
 * @return array массив лотов
 */
function get_lots(mysqli $connection): ?array
{
    $sql = "SELECT l.id, end_time, l.name, start_price, image, c.name AS category_name
                FROM lots l
                JOIN categories c ON l.category_id = c.id
                ORDER BY l.create_time DESC";
    $lots = db_fetch_data($connection, $sql);

    return $lots;
}

/**
 * Функция вывода лота по его идентификатору
 *
 * @param       $connection mysqli Ресурс соединения
 * @param       $id int идентификатор лота
 *
 * @return array массив лота
 */
function get_lot(mysqli $connection, int $id): ?array
{
    $sql = "SELECT l.name, l.id, user_id, end_time, start_price, step_rate, content, image, c.name AS category_name
               FROM lots l
               LEFT JOIN categories c ON l.category_id = c.id
               WHERE l.id = ?";
    $lot = db_fetch_data($connection, $sql, ['id' => $id], true);

    return $lot;
}

/**
 * Функция вывода лотов категории по идентификатору
 *
 * @param       $connection mysqli Ресурс соединения
 * @param       $id int идентификатор категории
 *
 * @return array массив лотов
 */
function get_lots_by_category(mysqli $connection, int $id): ?array
{
    $sql = "SELECT l.id, l.name, end_time, start_price, content, image, c.name AS category_name
                FROM lots l
                LEFT JOIN categories c ON l.category_id = c.id
                WHERE c.id = ?";
    $lots = db_fetch_data($connection, $sql, ['id' => $id]);

    return $lots;
}

/**
 * Функция добавления лота в БД
 *
 * @param       $connection mysqli Ресурс соединения
 * @return      $lot_data array массив данных лота
 *
 * @return integer идетификатор нового лота
 */
function insert_lot(mysqli $connection, array $lot_data): int
{
    $sql = "INSERT INTO lots (user_id, category_id, end_time, name, content, start_price, step_rate, image) VALUE (?, ?, ?, ?, ?, ?, ?, ?);";
    $add_lot = db_insert_data($connection, $sql, [
        'user_id'     => $lot_data['user_id'],
        'category_id' => $lot_data['category'],
        'end_time'    => $lot_data['end-time'],
        'name'        => $lot_data['name'],
        'content'     => $lot_data['content'],
        'start_price' => $lot_data['start-price'],
        'step_rate'   => $lot_data['step-rate'],
        'image'       => $lot_data['lot-image'],
    ]);

    return $add_lot;
}

/**
 * Функция добавления ставки в БД
 *
 * @param       $connection mysqli Ресурс соединения
 * @param       $user_data array данные ставки
 *
 * @return integer идентификатор новой ставки
 */
function insert_bet(mysqli $connection, array $bet_data): int
{
    $sql = "INSERT INTO bets (user_id, lot_id, amount) VALUE (?, ?, ?)";
    $add_bet = db_insert_data($connection, $sql, [
        'user_id' => $bet_data['user_id'],
        'lot_id'  => $bet_data['lot_id'],
        'amount'  => $bet_data['amount'],
    ]);

    return $add_bet;
}

/**
 * Функция возвращает текущую цену на лот
 *
 * @param       $connection mysqli Ресурс соединения
 * @param       $lot array массив данных лота
 *
 * @return int текущая цена лота
 */
function get_lot_price(mysqli $connection, array $lot): int
{
    $price = $lot['start_price'];
    $sql = "SELECT b.amount 
               FROM lots l
               LEFT JOIN bets b ON l.id = b.lot_id
               WHERE l.id = ?
               ORDER BY b.create_time DESC";
    $bets = db_fetch_data($connection, $sql, ['lot_id' => $lot['id']], true);

    if ($bets['amount']) {
        $price = $bets['amount'];
    }

    return $price;
}
<?php
/**
 * Возвращает все лоты из БД
 *
 * @param       mysqli $connection Ресурс соединения
 *
 * @return array|null Массив всех лотов
 */
function get_lots(mysqli $connection): ?array
{
    $sql
        = "SELECT l.id, end_time, l.name, l.winner_id, start_price, image, c.name AS category_name
                FROM lots l
                JOIN categories c ON l.category_id = c.id
                ORDER BY l.create_time DESC";
    $lots = db_fetch_data($connection, $sql);

    return $lots;
}

/**
 * Возвращает лот по его идентификатору
 *
 * @param       mysqli $connection Ресурс соединения
 * @param       int    $id         Идентификатор лота
 *
 * @return array|null Массив лота
 */
function get_lot(mysqli $connection, int $id): ?array
{
    $sql
        = "SELECT l.name, l.id, user_id, end_time, start_price, step_rate, content, image, c.name AS category_name
               FROM lots l
               LEFT JOIN categories c ON l.category_id = c.id
               WHERE l.id = ?";
    $lot = db_fetch_data($connection, $sql, ['id' => $id], true);

    return $lot;
}

/**
 *  Возвращает количество лотов в категории
 *
 * @param       mysqli $connection Ресурс соединения
 * @param       int    $id         идентификатор категории
 *
 * @return integer|null Количество лотов в категории
 */
function count_lots_by_category(mysqli $connection, int $id): ?int
{
    $sql
        = "SELECT COUNT(*) AS count FROM lots l
              WHERE l.category_id = ?";
    $count = db_fetch_data($connection, $sql, ['category_id' => $id], true);

    return $count['count'];
}

/**
 *  Возвращает все лоты категории по идентификатору категории
 *
 * @param       mysqli $connection Ресурс соединения
 * @param       int    $id         идентификатор категории
 *
 * @return array|null Массив лотов
 */
function get_lots_by_category_per_page(
    mysqli $connection,
    int $id,
    $limit,
    $offset
): ?array
{
    $sql
        = "SELECT l.id, l.name, end_time, start_price, content, image, c.name AS category_name
                FROM lots l
                LEFT JOIN categories c ON l.category_id = c.id
                WHERE c.id = ? LIMIT ? OFFSET ?";
    $lots = db_fetch_data($connection, $sql, [
        'id'     => $id,
        'LIMIT'  => $limit,
        'OFFSET' => $offset
    ]);

    return $lots;
}

/**
 * Добавляет новый лот в БД
 *
 * @param       mysqli $connection Ресурс соединения
 * @param       array  $lot_data   Данные нового лота
 *
 * @return integer|null Идетификатор нового лота
 */
function insert_lot(mysqli $connection, array $lot_data): ?int
{
    $sql
        = "INSERT INTO lots (user_id, category_id, end_time, name, content, start_price, step_rate, image) VALUE (?, ?, ?, ?, ?, ?, ?, ?);";

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
 * Добавления ставки в БД
 *
 * @param       mysqli $connection Ресурс соединения
 * @param       array  $user_data  Данные ставки
 *
 * @return integer Идентификатор новой ставки
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
 * Возвращает текущую цену на лот
 *
 * @param       mysqli $connection Ресурс соединения
 * @param       array  $lot        массив данных лота
 *
 * @return int Цена на лот
 */
function get_lot_price(mysqli $connection, array $lot): int
{
    $price = $lot['start_price'];
    $sql
        = "SELECT b.amount 
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

/**
 * Количество лотов соответствующее поисковому запросу
 *
 * @param       mysqli $connection Ресурс соединения
 * @param       string $search     Строка с поисковым запросом
 *
 * @return int|null Количество лотов
 */
function count_lots_by_search(mysqli $connection, string $search): ?int
{
    $sql
        = "SELECT COUNT(*) AS count FROM lots 
              WHERE MATCH(name, content) AGAINST(?)";

    $count = db_fetch_data($connection, $sql, ['lot_ft_search' => $search],
        true);

    return $count['count'];
}

/**
 * Поиск по названию и описанию лотов
 *
 * @param       mysqli $connection Ресурс соединения
 * @param       string $search     Строка с поисковым запросом
 * @param       int    $limit      Количество взвращаемых лотов на страницу
 * @param       int    $offset     Значение отступа возвращаемых лотов на страницу
 *
 * @return array|null Массив лотов
 */
function get_search_lots_by_page(
    mysqli $connection,
    string $search,
    int $limit,
    int $offset
): ?array
{
    $sql
        = "SELECT * FROM lots l 
              WHERE MATCH(name, content) AGAINST(?)
              ORDER BY l.create_time DESC LIMIT ? OFFSET ?";

    $find = db_fetch_data($connection, $sql, [
            'lot_ft_search' => $search,
            'LIMIT'         => $limit,
            'OFFSET'        => $offset
        ]
    );

    return $find;
}

/**
 * бновляет победителя лота
 *
 * @param       mysqli $connection Ресурс соединения
 * @param       array  $winner     Данные победителя
 *
 * @return int|null 
 */
function set_lot_winner(mysqli $connection, array $winner): ?int
{
    $sql = "UPDATE lots SET winner_id = ? WHERE id = ?";

    $add_winner = db_insert_data($connection, $sql, [
        'winner_id' => $winner['user_winner'],
        'id'        => $winner['lot_id'],
    ]);

    return $add_winner;
}
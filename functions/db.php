<?php
/**
 * Функция подключения к БД
 *
 * @param       $config_db array массив с данными на подключение к БД
 *
 * @return      $connection mysqli ресурс подключения к БД
 */
function db_connect(array $config_db): mysqli
{
    $connection = mysqli_connect(
        $config_db['host'],
        $config_db['user'],
        $config_db['password'],
        $config_db['db_name']
    );
    if (!$connection) {
        $error = mysqli_connect_error();
        die('Ошибка при подключении к БД: ' . $error);
    }
    mysqli_set_charset($connection, 'utf8');

    return $connection;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param       $link mysqli Ресурс соединения
 * @param       $sql  string SQL запрос с плейсхолдерами вместо значений
 * @param       $data array Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt(mysqli $connection, string $sql, array $data = []): mysqli_stmt
{
    $stmt = mysqli_prepare($connection, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: '
            . mysqli_error($connection);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            } else {
                if (is_string($value)) {
                    $type = 's';
                } else {
                    if (is_double($value)) {
                        $type = 'd';
                    }
                }
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($connection) > 0) {
            $errorMsg
                = 'Не удалось связать подготовленное выражение с параметрами: '
                . mysqli_error($connection);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Получение записей из БД
 *
 * @param       $connection mysqli Ресурс соединения
 * @param       $sql  string SQL запрос с плейсхолдерами вместо значений
 * @param       $data array данные для вставки на место плейсхолдеров
 * @param       $oneItem boolean флаг для вывода одной строки из базы
 *
 * @return array массив с данными из БД
 */
function db_fetch_data(mysqli $connection, string $sql, array $data = [], bool $oneItem = false): ?array
{
    $result = [];
    $stmt = db_get_prepare_stmt($connection, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($res) {
        if ($oneItem) {
            $result = mysqli_fetch_array($res, MYSQLI_ASSOC);
        } else {
            $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
        }
    }

    return $result;
}

/**
 * Добавление / Обновление / Удаление записей в БД
 *
 * @param       $connection mysqli Ресурс соединения
 * @param       $sql  string SQL запрос с плейсхолдерами вместо значений
 * @param       $data array Данные для вставки на место плейсхолдеров
 *
 * @return integer вернет идентификатор добалвеного елемента в талицу
 */
function db_insert_data(mysqli $connection, string $sql, array $data = []): ?int
{
    $stmt = db_get_prepare_stmt($connection, $sql, $data);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $result = mysqli_insert_id($connection);
    }

    return $result;
}

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
    $sql = "SELECT u.name AS user_name, b.amount, b.create_time
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
//    $sql = "SELECT * FROM bets b WHERE create_time = (SELECT MAX(create_time) FROM bets GROUP BY lot_id HAVING lot_id = b.lot_id)";

    $sql = "SELECT MAX(create_time) AS create_time, MAX(amount) AS amount FROM bets b GROUP BY lot_id";

    $bet = db_fetch_data($connection, $sql);

    return $bet;
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
    $sql = "SELECT l.id AS lot_id, l.name AS lot_name, image, end_time, c.name AS category_name, b.amount AS bet_amount
                FROM lots l
                LEFT JOIN categories c ON l.category_id = c.id
                LEFT JOIN bets b ON l.id = b.lot_id
                WHERE b.user_id = ?";
    $bets = db_fetch_data($connection, $sql, ['user_id' => $user_id]);

    return $bets;
}
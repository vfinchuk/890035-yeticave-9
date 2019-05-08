<?php
/**
 * Функция подключения к БД
 *
 * @param $config_db array массив с данными на подключение к БД
 *
 * @return $connection ресурс подключения к БД
 */

function db_connect($config_db)
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
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = [])
{
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: '
            . mysqli_error($link);
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

        if (mysqli_errno($link) > 0) {
            $errorMsg
                = 'Не удалось связать подготовленное выражение с параметрами: '
                . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Получение записей из БД
 *
 * @param       $link mysqli Ресурс соединения
 * @param       $sql  string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return array
 */
function db_fetch_data($link, $sql, $data = [], $oneItem = false)
{
    $result = [];
    $stmt = db_get_prepare_stmt($link, $sql, $data);
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
 * @param       $link mysqli Ресурс соединения
 * @param       $sql  string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return boolean
 */
function db_insert_data($link, $sql, $data = [])
{
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $result = mysqli_insert_id($link);
    }

    return $result;
}

/**
 * Функция вывода категорий
 *
 * @param $connection array ресурс соединения к БД
 *
 * @return array
 */
function get_categories($connection)
{
    $sql = "SELECT * FROM categories";
    $categories = db_fetch_data($connection, $sql);

    return $categories;
}

/**
 * Функция вывода категории по ID
 *
 * @param $connection array ресурс соединения к БД
 * @param $id string идентификатор категории
 *
 * @return array
 */
function get_category($connection, $id)
{
    $sql = "SELECT id, name, code FROM categories WHERE id = ?;";

    $category = db_fetch_data($connection, $sql, ['id' => $id], true);

    return $category;
}

/**
 * Функция вывода лотов
 *
 * @param $connection array ресурс соединения к БД
 *
 * @return array
 */
function get_lots($connection)
{
    $sql = "SELECT l.id, end_time, l.name, start_price, image, c.name AS category_name
                FROM lots l
                JOIN categories c ON l.category_id = c.id
                ORDER BY l.create_time DESC";

    $lots = db_fetch_data($connection, $sql);

    return $lots;
}

/**
 * Функция вывода одного лота по его ID
 *
 * @param $connection array ресурс соединения к БД
 * @param $id string идентификатор лота
 *
 * @return array
 */
function get_lot($connection, $id)
{
    $sql = "SELECT l.name, end_time, start_price, step_rate, content, image, c.name AS category_name
               FROM lots l
               LEFT JOIN categories c ON l.category_id = c.id
               WHERE l.id = ?";

    $lot = db_fetch_data($connection, $sql, ['id' => $id], true);

    return $lot;
}

/**
 * Функция вывода лотов категории по ID
 *
 * @param $connection array ресурс соединения к БД
 * @param $id string идентификатор категории
 *
 * @return array
 */
function get_lots_by_category($connection, $id)
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
 * @param $connection array ресурс соединения к БД
 * @return integer идентификатор добавленого лота
 *
 * @return integer идетификатор нового лота
 */
function insert_lot($connection, $lot_data)
{
    $sql = "INSERT INTO lots (user_id, category_id, end_time, name, content, start_price, step_rate, image) VALUE (?, ?, ?, ?, ?, ?, ?, ?);";

    $add_lot = db_insert_data($connection, $sql, [
        'user_id' => $lot_data['user_id'],
        'category_id'=> $lot_data['category'],
        'end_time' => $lot_data['end-time'],
        'name' => $lot_data['name'],
        'content' => $lot_data['content'],
        'start_price' => $lot_data['start-price'],
        'step_rate' => $lot_data['step-rate'],
        'image' => $lot_data['lot-image'],
    ]);

    return $add_lot;
    }

/**
 * Функция добавления пользователя в БД
 *
 * @param $connection array ресурс соединения к БД
 * @param array $user_data данные пользователя
 * @param string $avatar ссылка на аватар пользователя
 *
 * @return integer идетификатор нового пользователя
 */
function insert_user($connection, $user_data)
{
    $sql = "INSERT INTO users (email, password, name, contact, avatar) VALUE (?, ?, ?, ?, ?)";

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
 * Функция вывода пользователя по Email
 *
 * @param $connection array ресурс соединения к БД
 * @param string $email имейл для фильтрации
 *
 * @return array
 */
function get_user_by_email($connection, $email)
{
    $sql = "SELECT * FROM users WHERE email = ?;";
    $lots = db_fetch_data($connection, $sql, ['email' => $email], true);

    return $lots;
}

/**
 * Функция вывода категории по id
 *
 * @param $connection array ресурс соединения к БД
 * @param string $id идентификатор нужной категории
 *
 * @return array
 */
function get_category_by_id($connection, $id)
{

    $sql = "SELECT * FROM categories WHERE id = ?;";

    $category = db_fetch_data($connection, $sql, ['id' => $id]);

    return $category;
}


function get_password_by_email($connection, $email)
{
    $sql = "SELECT password FROM users WHERE users.email LIKE ?;";

    $password = db_fetch_data($connection, $sql, ['email' => $email], true);

    return $password;
}
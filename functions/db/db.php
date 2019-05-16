<?php
/**
 * Подключение к БД
 *
 * @param       array $config_db конфигурационный массив для подключения к БД
 *
 * @return      mysqli Ресурс соединения с БД
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
 * @param       mysqli $connection Ресурс соединения
 * @param       string $sql        SQL запрос с плейсхолдерами вместо значений
 * @param       array  $data       Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt(
    mysqli $connection,
    string $sql,
    array $data = []
): mysqli_stmt {
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
 * @param       mysqli $connection Ресурс соединения
 * @param       string $sql        SQL запрос с плейсхолдерами вместо значений
 * @param       array $data        Данные для вставки на место плейсхолдеров
 * @param       boolean $oneItem   Флаг вывода одной строки из базы
 *
 * @return array|null Массив данных из БД
 */
function db_fetch_data(
    mysqli $connection,
    string $sql,
    array $data = [],
    bool $oneItem = false
): ?array
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
 * @param       mysqli $connection Ресурс соединения
 * @param       string $sql        SQL запрос с плейсхолдерами вместо значений
 * @param       array  $data       Данные для вставки на место плейсхолдеров
 *
 * @return integer|null Идентификатор новой строки таблицы
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
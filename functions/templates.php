<?php
const SECOND_PER_HOUR = 3600;
const RUB = '<b class="rub">р</b>';
const rub = '<b> р</b>';

/**
 * Возвращает отформатированую цену
 *
 * @param       $price int для форматирования
 *
 * @return string отформатированая цена, пример: 25 489 ₽
 */
function price_format(int $price): string
{
    $price = strval(ceil($price));
    if ($price >= 1000) {
        $strend = substr($price, -3);
        $price = substr($price, 0, (strlen($price) - 3));
        $price .= ' ' . $strend;
    }
    return $price . RUB;
}


/**
 * Возвращает строку сколько часов и минут соталось до следующих суток
 *
 * @param       $endDate string для форматирования
 *
 * @return string времени до следующих суток
 */
function time_to_end(string $endDate): string
{
    $tsEnd = strtotime($endDate);
    $secToEnd = $tsEnd - time();

    if ($secToEnd <= 0) {
        return '00:00';
    }

    $hours = floor($secToEnd / SECOND_PER_HOUR);
    $minutes = floor(($secToEnd % SECOND_PER_HOUR) / 60);
    return sprintf('%02d', $hours) . ':' . sprintf('%02d', $minutes);

}

/**
 * Функция определяет остаток времени до конца суток
 *
 * @param       $endDate string дата в текстовом представлении
 * @param       $hours int сколько нужно отсчитать часов до конца суток. По умолчанию 1час.
 *
 * @return boolean
 */
function is_timer_finishing(string $endDate, int $hours = 1): bool
{
    $tsEnd = strtotime($endDate);
    $secToEnd = $tsEnd - time();
    if ($secToEnd <= 0) {
        return false;
    }
    $hoursToEnd = floor($secToEnd / SECOND_PER_HOUR);
    if ($hoursToEnd > $hours) {
        return false;
    }

    return true;
}

/**
 * Функция проверяет выиграла ли ставка пользователя
 *
 * @param       $lot_end_time string дата окончания ставки
 *
 * @return boolean вернет true если ставка выиграла иначе false
 */
function is_bet_end(string $bet_end_time): ?string
{
    $ts_lot_end_time = strtotime($bet_end_time);
    $ts_now = strtotime('now');
    if($ts_lot_end_time < $ts_now) {
        return 'rates__item--end';
    }

    return null;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form(
    int $number,
    string $one,
    string $two,
    string $many
): string {
    $number = (int)$number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

//function get_rate_time(string $create_time)
//{
//    $ts_create_time = strtotime($create_time);
//    $ts_now = strtotime('now');
//
//    $create_seconds = $ts_now - $ts_create_time;
//
//    $hours = floor($create_seconds / SECOND_PER_HOUR);
//    $minutes = floor($create_seconds / ($hours * SECOND_PER_HOUR)) / 60;
//
//    var_dump($create_time);
//    var_dump($hours);
//    var_dump($minutes);
//
//}


/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 *
 * @param       $name string Путь к файлу шаблона относительно папки templates
 * @param       $data array Ассоциативный массив с данными для шаблона
 *
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []): string
{
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}
<?php
const SECOND_PER_HOUR = 3600;
const RUB = '<b class="rub">р</b>';
const rub = '<b> р</b>';

/**
 * Возвращает отформатированую цену
 *
 * @param       $price int значение цены  для форматирования
 *
 * @return string  Пример: 25 489 ₽
 */
function price_format(int $price, $small_icon = false): string
{
    $price = strval(ceil($price));
    if ($price >= 1000) {
        $strend = substr($price, -3);
        $price = substr($price, 0, (strlen($price) - 3));
        $price .= ' ' . $strend;
    }
    if($small_icon) {
        return $price . rub;
    }

    return $price . RUB;
}

/**
 * Возвращает строку сколько часов и минут соталось до окончания
 *
 * @param       $endDate string Дата окончания лота
 *
 * @return string Часов и минут до окончания
 */
function time_to_end(string $end_date): string
{
    $tsEnd = strtotime($end_date);
    $secToEnd = $tsEnd - time();

    if ($secToEnd <= 0) {
        return '00:00';
    }

    $hours = floor($secToEnd / SECOND_PER_HOUR);
    $minutes = floor(($secToEnd % SECOND_PER_HOUR) / 60);
    return sprintf('%02d', $hours) . ':' . sprintf('%02d', $minutes);

}

/**
 * Определяет остаток времени до конца суток
 *
 * @param       $endDate string Дата в текстовом представлении
 * @param       $hours int Сколько нужно отсчитать часов до конца суток. По умолчанию 1час.
 *
 * @return boolean
 */
function is_timer_finishing(string $end_date, int $hours = 1): bool
{
    $tsEnd = strtotime($end_date);
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
 * Проверяет окончания ставки
 *
 * @param       $lot_end_time string Дата окончания ставки
 *
 * @return boolean Вернет true если срок действия лота окончен false
 */
function is_lot_end(string $bet_end_time): bool
{
    $ts_lot_end_time = strtotime($bet_end_time);
    $ts_now = strtotime('now');
    if ($ts_lot_end_time < $ts_now) {
        return true;
    }

    return false;
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

/**
 * Возвращает длительность от начала ставки в человеческом формате
 *
 * @param       $create_time string Дата создания ставки
 *
 * @return string Вернет длительность ставки
 */
function get_bet_time(string $create_time)
{
    $result = '';
    $ts_create_time = strtotime($create_time);
    $ts_now = strtotime('now');
    $seconds = $ts_now - $ts_create_time;

    $year = floor($seconds / (SECOND_PER_HOUR * 24 * 30 * 12));
    $months = floor($seconds / (SECOND_PER_HOUR * 24 * 30));
    $day = floor($seconds / (SECOND_PER_HOUR * 24));
    $hours = floor($seconds / SECOND_PER_HOUR);
    $mins = floor(($seconds - ($hours * SECOND_PER_HOUR)) / 60);
    $secs = floor($seconds % 60);

    $year_form = get_noun_plural_form($year, 'год', 'года', 'лет');
    $month_form = get_noun_plural_form($months, 'месяц', 'месяца', 'месяцев');
    $day_form = get_noun_plural_form($day, 'день', 'дня', 'дней');
    $hours_form = get_noun_plural_form($hours, 'час', 'часа', 'часов');
    $mins_form = get_noun_plural_form($mins, 'минута', 'минуты', 'минут');
    $secs_form = get_noun_plural_form($secs, 'секунда', 'секунды', 'секунд');

    if ($year == 1) {
        $result = $year_form;
    } elseif ($year > 1) {
        $result = $year . ' ' . $year_form;
    } elseif ($months == 1) {
        $result = $month_form;
    } elseif ($months > 1) {
        $result = $months . ' ' . $month_form;
    } elseif ($day == 1) {
        $result = $day_form;
    } elseif ($day > 1) {
        $result = $day . ' ' . $day_form;
    } elseif ($hours == 1) {
        $result = $hours_form;
    } elseif ($hours > 1) {
        $result = $hours . ' ' . $hours_form;
    } elseif ($mins == 1) {
        $result = $mins_form;
    } elseif ($mins > 1) {
        $result = $mins . ' ' . $mins_form;
    } elseif ($secs == 1) {
        $result = $secs_form;
    } elseif ($secs > 1) {
        $result = $secs . ' ' . $secs_form;
    }

    return $result . ' назад';
}

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
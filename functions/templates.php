<?php
const SECOND_PER_HOUR = 3600;
const RUB = '<b class="rub">р</b>';
const rub = '<b> р</b>';

/**
 * Возвращает отформатированую цену
 *
 * @param       int     $price      Цена для форматирования
 * @param       boolean $small_icon Флаг если нужно вернуть с маленьким знаком рубляБ пример: 25 000 р
 *
 * @return string Возвращает цену, пример: 25 000 ₽ | 25 000 р
 */
function price_format(int $price, $small_icon = false): string
{
    $price = strval(ceil($price));
    if ($price >= 1000) {
        $strend = substr($price, -3);
        $price = substr($price, 0, (strlen($price) - 3));
        $price .= ' ' . $strend;
    }
    if ($small_icon) {
        return $price . rub;
    }

    return $price . RUB;
}

/**
 * Возвращает количество часов и минут до даты завершения
 *
 * @param       string $end_date Дата завершения
 *
 * @return string Осталось часов:минут до окончания
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
 * Определяет остаток времени до конца суток установленной даты
 *
 * @param       string $end_date Дата завершения
 * @param       int    $hours    Значение остатка до следующих суток, по умолчанию 1
 *
 * @return boolean Вернет true когда до даты завершения останется меньше 1го часа иначе false
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
 * @param       string $lot_end_time Дата завершения ставки
 *
 * @return boolean Вернет true если срок действия лота окончен иначе false
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
 * @param       int    $number Число, по которому вычисляем форму множественного числа
 * @param       string $one    Форма единственного числа: яблоко, час, минута
 * @param       string $two    Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param       string $many   Форма множественного числа для остальных чисел
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
 * Возвращает пройденого времени от начала ставки в человеческом формате
 *
 * @param       string $create_time Дата добавления ставки
 *
 * @return string количество пройденного времени, пример: 5 минут назад
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
 * Постраницая пагинация
 *
 * @param       array $lots_count Колличество лотов
 * @param             $per_page   int Количество лотов вывода на страницу
 *
 * @return array|null Вернет массив данных пагинации
 */
function pagination(int $lots_count, int $per_page): ?array
{
    $pag['current_page'] = $_GET['page'] ?? 1;
    $pag['offset'] = ($pag['current_page'] - 1) * $per_page;
    $pag['pages_count'] = ceil($lots_count / $per_page);;
    $pag['pages'] = range(1, $pag['pages_count']);
    $pag['cur_page'] = $pag['current_page'];
    $pag['prev_page'] = ($pag['current_page'] > 1) ? $pag['current_page'] - 1
        : $pag['current_page'];
    $pag['next_page'] = (count($pag['pages']) > $pag['current_page'])
        ? $pag['current_page'] + 1 : $pag['current_page'];

    return $pag;
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 *
 * @param       string $name Путь к файлу шаблона относительно папки templates
 * @param       array  $data Ассоциативный массив с данными для шаблона
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
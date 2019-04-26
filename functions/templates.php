<?php
const SECOND_PER_HOUR = 3600;
const RUB = '<b class="rub">р</b>';

/**
 * Возвращает отформатированую цену
 *
 * @param int $price для форматирования
 *
 * @return string отформатированая цена, пример: 25 489 ₽
 */
function price_format($price)
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
 * @return string
 */
function time_to_end($endDate)
{
    $tsEnd = strtotime($endDate);
    $secToEnd = $tsEnd - time();

    if ($secToEnd <= 0) {
        return '00:00';
    }

    $hours = floor($secToEnd / SECOND_PER_HOUR);
    $minutes = floor(($secToEnd % SECOND_PER_HOUR ) / 60);
    $minutes = $minutes < 10 ? '0' . $minutes : $minutes;
    return $hours . ':' . $minutes;

}

/**
 * Вернет true когда останется до следующих суток меньше чем $hours
 *
 * @param string $endDate дата в текстовом представлении
 * @param int $hours количество часов до конца суток. Равен 1 по умолчанию
 *
 * @return boolean
 */
function is_timer_finishing($endDate, $hours = 1)
{
    $tsEnd = strtotime($endDate);
    $secToEnd = $tsEnd - time();

    if($secToEnd <= 0){
        return false;
    }

    $hoursToEnd = floor($secToEnd / SECOND_PER_HOUR);

    if($hoursToEnd > $hours){
        return false;
    }

    return true;
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
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 *
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 *
 * @return string Итоговый HTML
 */
function include_template($name, array $data = [])
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
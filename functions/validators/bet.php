<?php

/**
 * Функция валидации формы новой ставки
 *
 * @param       $lot_data array массив данных лота
 * @param       $amount array размер ставки
 *
 * @return array вернет null или массив ошибок
 */
function validate_bet_form(array $lot_data, string $amount): ?array
{
    $errors = [];

    if (empty($amount)) {
        $errors['bet'] = 'Введите сумму ставки';
    } else {

        if (!is_numeric($amount)) {
            $errors['bet'] = 'Только числовое значение';
        } elseif (($lot_data['price'] + $lot_data['step_rate']) > $amount) {
            $errors['bet'] = 'Минимальная ставка на этот товар ' . ($lot_data['price'] + $lot_data['step_rate']) . ' ';
            $errors['bet'] .= get_noun_plural_form($lot_data['step_rate'], 'рубль', 'рубля', 'рублей');
        }
    }

    if (count($errors)) {
        return $errors;
    }

    return null;
}

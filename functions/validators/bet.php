<?php
/**
 * Валидация полей формы на добавления новой ставки
 *
 * @param       array $lot_data Массив данных лота
 * @param       array $amount   Размер ставки
 *
 * @return array|null Массив с ошибками
 */
function validate_bet_form(array $lot_data, string $amount): ?array
{
    $errors = [];

    if (empty($amount)) {
        $errors['bet'] = 'Введите сумму ставки';
    } else {

        if (!is_numeric($amount)) {
            $errors['bet'] = 'Только числовое значение';
        } else {

            if (($lot_data['price'] + $lot_data['step_rate']) > $amount) {
                $errors['bet'] = 'Минимальная ставка на этот товар '
                    . ($lot_data['price'] + $lot_data['step_rate']) . ' ';
                $errors['bet'] .= get_noun_plural_form($lot_data['step_rate'],
                    'рубль', 'рубля', 'рублей');
            }

            if($amount > 99999) {
                $errors['bet'] = 'Максимальнодопустимое значение ставки не должно привышать 99999 рублей';
            }

        }
    }

    if (count($errors)) {
        return $errors;
    }

    return null;
}

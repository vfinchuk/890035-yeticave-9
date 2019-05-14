<?php
/**
 * Функция фильтрации данных из формы
 *
 * @param       $form_data array Массив данных из формы
 *
 * @param return array Массив отфильтрованных данных из формы
 */
function filter_form_data(array $form_data): array
{
    $filter = [];
    foreach ($form_data as $form_key => $form_item) {
        if (!empty($form_item)) {
            $filter[$form_key] = htmlspecialchars($form_item);
        }
    }
    return $filter;
}




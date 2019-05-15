<?php
/**
 * Фильтрация массива данных формы
 *
 * @param       array $form_data Массив данных формы
 *
 * @return array Массив отфильтрованных данных формы
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

/**
 * Фильтрация поискового запроса
 *
 * @param       string $search данные поискового запроса
 *
 * @return string отфильтрованный поисковый запрос
 */
function filter_search_query(string $search): string
{
    return htmlspecialchars(trim($search));
}


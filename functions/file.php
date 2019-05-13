<?php
/**
 * Сохранение изображения в uploads
 *
 * @param       $image array массив загружаемого изображения
 *
 * @return string ссылка на изображение
 */
function upload_file(array $image): ?string
{
    $tmp_name = $image['tmp_name'] ?? null;
    if (empty($tmp_name)) {
        return null;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($finfo, $tmp_name);

    $file_type = str_replace('/', '', strstr($file_type, '/'));
    $file_name = uniqid() . '.' . $file_type;

    $file_path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'uploads/';
    $file_link = DIRECTORY_SEPARATOR . 'uploads/' . $file_name;

    if ($tmp_name) {
        if (!move_uploaded_file($tmp_name, $file_path . $file_name)) {
            die('Ошибка при сохранении файла');
        }
    } else {
        return null;
    }

    return $file_link;
}
<?php
include_once(__DIR__ . '/bootstrap.php');

$lot_id = $_GET['id'] ?? null;
$categories = get_categories($connection);
$lot = get_lot($connection, intval($lot_id));
$bets = get_bets_by_lot($connection, intval($lot['id']));

if ($bets) {
    $bets = array_slice($bets, 0, 10);
}

if ($lot) {

    $title = "Лот - {$lot['name']}";

    $lot['price'] = get_lot_price($connection, $lot);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $bet_data = $_POST['bet'] ?? null;
        if (!$bet_data) {
            die('Отсутствуют данные о ставке в запросе');
        }

        $bet_data['lot_id'] = $lot['id'];
        $bet_data['user_id'] = $user['id'];

        $errors = validate_bet_form($lot, $bet_data['amount']);

        if ($errors) {

            $content = include_template('lot.php', [
                'categories' => $categories,
                'lot'        => $lot,
                'user'       => $user,
                'bets'       => $bets,
                'errors'     => $errors
            ]);
        } else {
            $lot_data = filter_form_data($bet_data);

            $bets = insert_bet($connection, $bet_data);
            header('Location: lot.php?id=' . $lot_id);
        }

        var_dump($lot['price']);

    } else {

        $content = include_template('lot.php', [
            'categories' => $categories,
            'lot'        => $lot,
            'user'       => $user,
            'bets'       => $bets
        ]);

    }

} else {

    $title = 'Лот не найден.';
    http_response_code(404);
    $content = include_template('404.php', [
        'categories' => $categories
    ]);

}

$layout = include_template('layout.php', [
    'title'      => $title,
    'categories' => $categories,
    'content'    => $content,
    'user'       => $user,

]);


print $layout;
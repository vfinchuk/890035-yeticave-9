<?php
$transport = new Swift_SmtpTransport('phpdemo.ru', 25);
$transport->setUsername('keks@phpdemo.ru');
$transport->setPassword('htmlacademy');

$mailer = new Swift_Mailer($transport);

$logger = new Swift_Plugins_Loggers_ArrayLogger();
$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

$winners = get_winners($connection);
$recipients = [];

if (!empty($winners)) {

    foreach ($winners as $winner) {

        $set_winner = set_lot_winner($connection, $winner);

        $recipients[$winner['email']] = $winner['name'];
    }

    $message = new Swift_Message();
    $message->setSubject('Оповещение о выигрыше лота');
    $message->setFrom(['keks@phpdemo.ru' => 'Yeticave']);
    $message->setBcc($recipients);

    $msg_content = include_template('email.php', [
        'winners' => $winners
    ]);
    $message->setBody($msg_content, 'text/html');

    $result = $mailer->send($message);

//    if ($result) {
//        print("Рассылка успешно отправлена");
//    }
//    else {
//        print("Не удалось отправить рассылку: " . $logger->dump());
//    }

}

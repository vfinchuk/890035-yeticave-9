<?php

/* dev-configuration */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!file_exists('config.php')) {
    die('Создайте и сконфигурируйте файл config.php на основе файла config.sample.php');
}

$config = require_once(__DIR__ . '/config.php');

/* Get site timezone */
date_default_timezone_set($config['timezone']);

if (!file_exists('vendor/autoload.php')) {
    die(' Установите composer в корневую директорию сайта');
}

require_once(__DIR__ . '/vendor/autoload.php');

require_once(__DIR__ . '/functions/db/db.php');
require_once(__DIR__ . '/functions/db/category.php');
require_once(__DIR__ . '/functions/db/bet.php');
require_once(__DIR__ . '/functions/db/lot.php');
require_once(__DIR__ . '/functions/db/user.php');

require_once(__DIR__ . '/functions/file.php');
require_once(__DIR__ . '/functions/templates.php');

require_once(__DIR__ . '/functions/validators/validators.php');
require_once(__DIR__ . '/functions/validators/user.php');
require_once(__DIR__ . '/functions/validators/lot.php');
require_once(__DIR__ . '/functions/validators/bet.php');

$connection = db_connect($config['db']);

$user = $_SESSION['user'] ?? null;

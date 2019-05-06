<?php

/* dev-configuration */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$config = require(__DIR__ . '/config.php');


/* Get site timezone */
date_default_timezone_set($config['timezone']);


require_once(__DIR__ . '/functions/db.php');
require_once(__DIR__ . '/functions/file.php');
require_once(__DIR__ . '/functions/templates.php');
require_once(__DIR__ . '/functions/validators.php');

$connection = db_connect($config['db']);

$isAuth = rand(0, 1);
$userName = 'Vova';
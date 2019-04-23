<?php

/* dev-configuration */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$config = require(__DIR__ . '/config.sample.php');


/* Get site timezone */
date_default_timezone_set($config['timezone']);


/* Data Base */
require_once(__DIR__ . '/functions/db.php');

/* Template function */
require_once(__DIR__ . '/functions/templates.php');

/* validators */
require_once(__DIR__ . '/functions/validators.php');


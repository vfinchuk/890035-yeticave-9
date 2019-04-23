<?php

/* dev-configuration */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$config = require(__DIR__ . '/config.sample.php');


/* Get site timezone */
date_default_timezone_set($config['timezone']);


/* Settings */
require_once(__DIR__ . '/includes/options.php');

/* Include DB site */
require_once(__DIR__ . '/includes/data.php');

/* Functions */
require_once(__DIR__ . '/functions/db.php');
require_once(__DIR__ . '/functions/templates.php');
require_once(__DIR__ . '/functions/validators.php');


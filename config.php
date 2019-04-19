<?php

/* dev-configuration */
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


/* Settings */
require_once(__DIR__ . '/includes/options.php');

/* Include DB site */
require_once(__DIR__ . '/includes/data.php');

/* Functions */
require_once(__DIR__ . '/helpers.php');